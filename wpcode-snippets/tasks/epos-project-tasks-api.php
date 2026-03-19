<?php
/**
 * EPOS Project Tasks API
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_project_tasks_register_table_migration')) {
    function epos_project_tasks_register_table_migration() {
        epos_register_migration('epos_db_009_project_tasks', function () {
            global $wpdb;

            $table_name = epos_get_table_name('project_tasks');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                project_id BIGINT UNSIGNED NULL,
                stage_id BIGINT UNSIGNED NULL,
                assigned_user_id BIGINT UNSIGNED NULL,
                title VARCHAR(190) NOT NULL,
                description LONGTEXT NULL,
                priority VARCHAR(50) NOT NULL DEFAULT 'normal',
                status VARCHAR(50) NOT NULL DEFAULT 'open',
                due_date DATETIME NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_project_id (project_id),
                KEY idx_stage_id (stage_id),
                KEY idx_assigned_user_id (assigned_user_id),
                KEY idx_priority (priority),
                KEY idx_status (status),
                KEY idx_title (title)
            ) {$charset};";

            dbDelta($sql);
        });
    }
}
add_action('init', 'epos_project_tasks_register_table_migration', 9);

if (!function_exists('epos_project_tasks_api_permission')) {
    function epos_project_tasks_api_permission() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_project_tasks_table')) {
    function epos_project_tasks_table() {
        return epos_get_table_name('project_tasks');
    }
}

if (!function_exists('epos_project_tasks_sanitize_payload')) {
    function epos_project_tasks_sanitize_payload($data) {
        $data = is_array($data) ? $data : array();

        return array(
            'project_id'        => isset($data['project_id']) ? absint($data['project_id']) : 0,
            'stage_id'          => isset($data['stage_id']) ? absint($data['stage_id']) : 0,
            'assigned_user_id'  => isset($data['assigned_user_id']) ? absint($data['assigned_user_id']) : 0,
            'title'             => isset($data['title']) ? sanitize_text_field($data['title']) : '',
            'description'       => isset($data['description']) ? sanitize_textarea_field($data['description']) : '',
            'priority'          => isset($data['priority']) ? sanitize_text_field($data['priority']) : 'normal',
            'status'            => isset($data['status']) ? sanitize_text_field($data['status']) : 'open',
            'due_date'          => isset($data['due_date']) ? sanitize_text_field($data['due_date']) : null,
        );
    }
}

if (!function_exists('epos_project_tasks_format_response')) {
    function epos_project_tasks_format_response($success, $message, $data = null, $status = 200) {
        return new WP_REST_Response(array(
            'success' => (bool) $success,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }
}

if (!function_exists('epos_project_tasks_list')) {
    function epos_project_tasks_list(WP_REST_Request $request) {
        global $wpdb;

        $table            = epos_project_tasks_table();
        $page             = max(1, absint($request->get_param('page')));
        $per_page         = max(1, min(100, absint($request->get_param('per_page')) ?: 20));
        $offset           = ($page - 1) * $per_page;
        $search           = sanitize_text_field((string) $request->get_param('search'));
        $status           = sanitize_text_field((string) $request->get_param('status'));
        $priority         = sanitize_text_field((string) $request->get_param('priority'));
        $project_id       = absint($request->get_param('project_id'));
        $stage_id         = absint($request->get_param('stage_id'));
        $assigned_user_id = absint($request->get_param('assigned_user_id'));

        $where  = array('1=1');
        $params = array();

        if ($search !== '') {
            $where[] = '(title LIKE %s OR description LIKE %s)';
            $like = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $like;
            $params[] = $like;
        }

        if ($status !== '') {
            $where[] = 'status = %s';
            $params[] = $status;
        }

        if ($priority !== '') {
            $where[] = 'priority = %s';
            $params[] = $priority;
        }

        if ($project_id > 0) {
            $where[] = 'project_id = %d';
            $params[] = $project_id;
        }

        if ($stage_id > 0) {
            $where[] = 'stage_id = %d';
            $params[] = $stage_id;
        }

        if ($assigned_user_id > 0) {
            $where[] = 'assigned_user_id = %d';
            $params[] = $assigned_user_id;
        }

        $where_sql = implode(' AND ', $where);
        $count_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where_sql}";
        $data_sql  = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY id DESC LIMIT %d OFFSET %d";

        $count_query = !empty($params) ? $wpdb->prepare($count_sql, $params) : $count_sql;

        $data_params = $params;
        $data_params[] = $per_page;
        $data_params[] = $offset;
        $data_query = $wpdb->prepare($data_sql, $data_params);

        $total   = (int) $wpdb->get_var($count_query);
        $results = $wpdb->get_results($data_query, ARRAY_A);

        return epos_project_tasks_format_response(true, 'Project tasks retrieved successfully.', array(
            'items' => $results,
            'pagination' => array(
                'page'     => $page,
                'per_page' => $per_page,
                'total'    => $total,
                'pages'    => $per_page > 0 ? (int) ceil($total / $per_page) : 1,
            ),
        ));
    }
}

if (!function_exists('epos_project_tasks_get')) {
    function epos_project_tasks_get(WP_REST_Request $request) {
        global $wpdb;

        $table = epos_project_tasks_table();
        $id    = absint($request['id']);

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$item) {
            return epos_project_tasks_format_response(false, 'Project task not found.', null, 404);
        }

        return epos_project_tasks_format_response(true, 'Project task retrieved successfully.', $item);
    }
}

if (!function_exists('epos_project_tasks_create')) {
    function epos_project_tasks_create(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_project_tasks_table();
        $payload = epos_project_tasks_sanitize_payload($request->get_json_params());

        if ($payload['title'] === '') {
            return epos_project_tasks_format_response(false, 'The title field is required.', null, 400);
        }

        $payload['project_id']       = $payload['project_id'] > 0 ? $payload['project_id'] : null;
        $payload['stage_id']         = $payload['stage_id'] > 0 ? $payload['stage_id'] : null;
        $payload['assigned_user_id'] = $payload['assigned_user_id'] > 0 ? $payload['assigned_user_id'] : null;
        $payload['due_date']         = !empty($payload['due_date']) ? $payload['due_date'] : null;
        $payload['created_at']       = current_time('mysql');
        $payload['updated_at']       = current_time('mysql');

        $inserted = $wpdb->insert(
            $table,
            $payload,
            array('%d','%d','%d','%s','%s','%s','%s','%s','%s','%s')
        );

        if ($inserted === false) {
            return epos_project_tasks_format_response(false, 'Failed to create project task.', null, 500);
        }

        $item_id = (int) $wpdb->insert_id;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $item_id),
            ARRAY_A
        );

        return epos_project_tasks_format_response(true, 'Project task created successfully.', $item, 201);
    }
}

if (!function_exists('epos_project_tasks_update')) {
    function epos_project_tasks_update(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_project_tasks_table();
        $id      = absint($request['id']);
        $payload = epos_project_tasks_sanitize_payload($request->get_json_params());

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );

        if (!$exists) {
            return epos_project_tasks_format_response(false, 'Project task not found.', null, 404);
        }

        if ($payload['title'] === '') {
            return epos_project_tasks_format_response(false, 'The title field is required.', null, 400);
        }

        $payload['project_id']       = $payload['project_id'] > 0 ? $payload['project_id'] : null;
        $payload['stage_id']         = $payload['stage_id'] > 0 ? $payload['stage_id'] : null;
        $payload['assigned_user_id'] = $payload['assigned_user_id'] > 0 ? $payload['assigned_user_id'] : null;
        $payload['due_date']         = !empty($payload['due_date']) ? $payload['due_date'] : null;
        $payload['updated_at']       = current_time('mysql');

        $updated = $wpdb->update(
            $table,
            $payload,
            array('id' => $id),
            array('%d','%d','%d','%s','%s','%s','%s','%s','%s'),
            array('%d')
        );

        if ($updated === false) {
            return epos_project_tasks_format_response(false, 'Failed to update project task.', null, 500);
        }

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return epos_project_tasks_format_response(true, 'Project task updated successfully.', $item);
    }
}

if (!function_exists('epos_register_project_tasks_routes')) {
    function epos_register_project_tasks_routes() {
        register_rest_route(epos_get_rest_namespace(), '/project-tasks', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_project_tasks_list',
                'permission_callback' => 'epos_project_tasks_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'epos_project_tasks_create',
                'permission_callback' => 'epos_project_tasks_api_permission',
            ),
        ));

        register_rest_route(epos_get_rest_namespace(), '/project-tasks/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_project_tasks_get',
                'permission_callback' => 'epos_project_tasks_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'epos_project_tasks_update',
                'permission_callback' => 'epos_project_tasks_api_permission',
            ),
        ));
    }
}
add_action('rest_api_init', 'epos_register_project_tasks_routes');
