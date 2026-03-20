<?php
/**
 * EPOS Project Stages API
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_project_stages_register_table_migration')) {
    function epos_project_stages_register_table_migration() {
        epos_register_migration('epos_db_018_project_stages', function () {
            global $wpdb;

            $table_name = epos_get_table_name('project_stages');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                project_id BIGINT UNSIGNED NULL,
                name VARCHAR(190) NOT NULL,
                slug VARCHAR(190) NULL,
                stage_order INT NULL DEFAULT 0,
                status VARCHAR(50) NOT NULL DEFAULT 'active',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_project_id (project_id),
                KEY idx_slug (slug),
                KEY idx_stage_order (stage_order),
                KEY idx_status (status)
            ) {$charset};";

            dbDelta($sql);
        });
    }
}
add_action('init', 'epos_project_stages_register_table_migration', 18);

if (!function_exists('epos_project_stages_api_permission')) {
    function epos_project_stages_api_permission() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_project_stages_table')) {
    function epos_project_stages_table() {
        return epos_get_table_name('project_stages');
    }
}

if (!function_exists('epos_project_stages_sanitize_payload')) {
    function epos_project_stages_sanitize_payload($data) {
        $data = is_array($data) ? $data : array();

        return array(
            'project_id'   => isset($data['project_id']) ? absint($data['project_id']) : 0,
            'name'         => isset($data['name']) ? sanitize_text_field($data['name']) : '',
            'slug'         => isset($data['slug']) ? sanitize_title($data['slug']) : '',
            'stage_order'  => isset($data['stage_order']) ? intval($data['stage_order']) : 0,
            'status'       => isset($data['status']) ? sanitize_text_field($data['status']) : 'active',
        );
    }
}

if (!function_exists('epos_project_stages_format_response')) {
    function epos_project_stages_format_response($success, $message, $data = null, $status = 200) {
        return new WP_REST_Response(array(
            'success' => (bool) $success,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }
}

if (!function_exists('epos_project_stages_list')) {
    function epos_project_stages_list(WP_REST_Request $request) {
        global $wpdb;

        $table      = epos_project_stages_table();
        $page       = max(1, absint($request->get_param('page')));
        $per_page   = max(1, min(100, absint($request->get_param('per_page')) ?: 20));
        $offset     = ($page - 1) * $per_page;
        $project_id = absint($request->get_param('project_id'));
        $status     = sanitize_text_field((string) $request->get_param('status'));
        $search     = sanitize_text_field((string) $request->get_param('search'));

        $where  = array('1=1');
        $params = array();

        if ($project_id > 0) {
            $where[] = 'project_id = %d';
            $params[] = $project_id;
        }

        if ($status !== '') {
            $where[] = 'status = %s';
            $params[] = $status;
        }

        if ($search !== '') {
            $where[] = '(name LIKE %s OR slug LIKE %s)';
            $like = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $where_sql = implode(' AND ', $where);
        $count_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where_sql}";
        $data_sql  = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY stage_order ASC, id ASC LIMIT %d OFFSET %d";

        $count_query = !empty($params) ? $wpdb->prepare($count_sql, $params) : $count_sql;

        $data_params = $params;
        $data_params[] = $per_page;
        $data_params[] = $offset;
        $data_query = $wpdb->prepare($data_sql, $data_params);

        $total   = (int) $wpdb->get_var($count_query);
        $results = $wpdb->get_results($data_query, ARRAY_A);

        return epos_project_stages_format_response(true, 'Project stages retrieved successfully.', array(
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

if (!function_exists('epos_project_stages_get')) {
    function epos_project_stages_get(WP_REST_Request $request) {
        global $wpdb;

        $table = epos_project_stages_table();
        $id    = absint($request['id']);

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$item) {
            return epos_project_stages_format_response(false, 'Project stage not found.', null, 404);
        }

        return epos_project_stages_format_response(true, 'Project stage retrieved successfully.', $item);
    }
}

if (!function_exists('epos_project_stages_create')) {
    function epos_project_stages_create(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_project_stages_table();
        $payload = epos_project_stages_sanitize_payload($request->get_json_params());

        if ($payload['name'] === '') {
            return epos_project_stages_format_response(false, 'The name field is required.', null, 400);
        }

        $payload['project_id'] = $payload['project_id'] > 0 ? $payload['project_id'] : null;
        $payload['created_at'] = current_time('mysql');
        $payload['updated_at'] = current_time('mysql');

        $inserted = $wpdb->insert(
            $table,
            $payload,
            array('%d','%s','%s','%d','%s','%s','%s')
        );

        if ($inserted === false) {
            return epos_project_stages_format_response(false, 'Failed to create project stage.', null, 500);
        }

        $item_id = (int) $wpdb->insert_id;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $item_id),
            ARRAY_A
        );

        return epos_project_stages_format_response(true, 'Project stage created successfully.', $item, 201);
    }
}

if (!function_exists('epos_project_stages_update')) {
    function epos_project_stages_update(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_project_stages_table();
        $id      = absint($request['id']);
        $payload = epos_project_stages_sanitize_payload($request->get_json_params());

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );

        if (!$exists) {
            return epos_project_stages_format_response(false, 'Project stage not found.', null, 404);
        }

        if ($payload['name'] === '') {
            return epos_project_stages_format_response(false, 'The name field is required.', null, 400);
        }

        $payload['project_id'] = $payload['project_id'] > 0 ? $payload['project_id'] : null;
        $payload['updated_at'] = current_time('mysql');

        $updated = $wpdb->update(
            $table,
            $payload,
            array('id' => $id),
            array('%d','%s','%s','%d','%s','%s'),
            array('%d')
        );

        if ($updated === false) {
            return epos_project_stages_format_response(false, 'Failed to update project stage.', null, 500);
        }

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return epos_project_stages_format_response(true, 'Project stage updated successfully.', $item);
    }
}

if (!function_exists('epos_register_project_stages_routes')) {
    function epos_register_project_stages_routes() {
        register_rest_route(epos_get_rest_namespace(), '/project-stages', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_project_stages_list',
                'permission_callback' => 'epos_project_stages_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'epos_project_stages_create',
                'permission_callback' => 'epos_project_stages_api_permission',
            ),
        ));

        register_rest_route(epos_get_rest_namespace(), '/project-stages/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_project_stages_get',
                'permission_callback' => 'epos_project_stages_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'epos_project_stages_update',
                'permission_callback' => 'epos_project_stages_api_permission',
            ),
        ));
    }
}
add_action('rest_api_init', 'epos_register_project_stages_routes');
