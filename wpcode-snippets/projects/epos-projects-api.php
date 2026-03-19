<?php
/**
 * EPOS Projects API
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_projects_register_table_migration')) {
    function epos_projects_register_table_migration() {
        epos_register_migration('epos_db_008_projects', function () {
            global $wpdb;

            $table_name = epos_get_table_name('projects');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                client_id BIGINT UNSIGNED NULL,
                opportunity_id BIGINT UNSIGNED NULL,
                contract_id BIGINT UNSIGNED NULL,
                business_id BIGINT UNSIGNED NULL,
                title VARCHAR(190) NOT NULL,
                description LONGTEXT NULL,
                address VARCHAR(255) NULL,
                status VARCHAR(50) NOT NULL DEFAULT 'new',
                start_date DATE NULL,
                end_date DATE NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_client_id (client_id),
                KEY idx_opportunity_id (opportunity_id),
                KEY idx_contract_id (contract_id),
                KEY idx_business_id (business_id),
                KEY idx_status (status),
                KEY idx_title (title)
            ) {$charset};";

            dbDelta($sql);
        });
    }
}
add_action('init', 'epos_projects_register_table_migration', 8);

if (!function_exists('epos_projects_api_permission')) {
    function epos_projects_api_permission() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_projects_table')) {
    function epos_projects_table() {
        return epos_get_table_name('projects');
    }
}

if (!function_exists('epos_projects_sanitize_payload')) {
    function epos_projects_sanitize_payload($data) {
        $data = is_array($data) ? $data : array();

        return array(
            'client_id'      => isset($data['client_id']) ? absint($data['client_id']) : 0,
            'opportunity_id' => isset($data['opportunity_id']) ? absint($data['opportunity_id']) : 0,
            'contract_id'    => isset($data['contract_id']) ? absint($data['contract_id']) : 0,
            'business_id'    => isset($data['business_id']) ? absint($data['business_id']) : 0,
            'title'          => isset($data['title']) ? sanitize_text_field($data['title']) : '',
            'description'    => isset($data['description']) ? sanitize_textarea_field($data['description']) : '',
            'address'        => isset($data['address']) ? sanitize_text_field($data['address']) : '',
            'status'         => isset($data['status']) ? sanitize_text_field($data['status']) : 'new',
            'start_date'     => isset($data['start_date']) ? sanitize_text_field($data['start_date']) : null,
            'end_date'       => isset($data['end_date']) ? sanitize_text_field($data['end_date']) : null,
        );
    }
}

if (!function_exists('epos_projects_format_response')) {
    function epos_projects_format_response($success, $message, $data = null, $status = 200) {
        return new WP_REST_Response(array(
            'success' => (bool) $success,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }
}

if (!function_exists('epos_projects_list')) {
    function epos_projects_list(WP_REST_Request $request) {
        global $wpdb;

        $table          = epos_projects_table();
        $page           = max(1, absint($request->get_param('page')));
        $per_page       = max(1, min(100, absint($request->get_param('per_page')) ?: 20));
        $offset         = ($page - 1) * $per_page;
        $search         = sanitize_text_field((string) $request->get_param('search'));
        $status         = sanitize_text_field((string) $request->get_param('status'));
        $client_id      = absint($request->get_param('client_id'));
        $opportunity_id = absint($request->get_param('opportunity_id'));
        $contract_id    = absint($request->get_param('contract_id'));
        $business_id    = absint($request->get_param('business_id'));

        $where  = array('1=1');
        $params = array();

        if ($search !== '') {
            $where[] = '(title LIKE %s OR description LIKE %s OR address LIKE %s)';
            $like = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if ($status !== '') {
            $where[] = 'status = %s';
            $params[] = $status;
        }

        if ($client_id > 0) {
            $where[] = 'client_id = %d';
            $params[] = $client_id;
        }

        if ($opportunity_id > 0) {
            $where[] = 'opportunity_id = %d';
            $params[] = $opportunity_id;
        }

        if ($contract_id > 0) {
            $where[] = 'contract_id = %d';
            $params[] = $contract_id;
        }

        if ($business_id > 0) {
            $where[] = 'business_id = %d';
            $params[] = $business_id;
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

        return epos_projects_format_response(true, 'Projects retrieved successfully.', array(
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

if (!function_exists('epos_projects_get')) {
    function epos_projects_get(WP_REST_Request $request) {
        global $wpdb;

        $table = epos_projects_table();
        $id    = absint($request['id']);

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$item) {
            return epos_projects_format_response(false, 'Project not found.', null, 404);
        }

        return epos_projects_format_response(true, 'Project retrieved successfully.', $item);
    }
}

if (!function_exists('epos_projects_create')) {
    function epos_projects_create(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_projects_table();
        $payload = epos_projects_sanitize_payload($request->get_json_params());

        if ($payload['title'] === '') {
            return epos_projects_format_response(false, 'The title field is required.', null, 400);
        }

        $payload['client_id']      = $payload['client_id'] > 0 ? $payload['client_id'] : null;
        $payload['opportunity_id'] = $payload['opportunity_id'] > 0 ? $payload['opportunity_id'] : null;
        $payload['contract_id']    = $payload['contract_id'] > 0 ? $payload['contract_id'] : null;
        $payload['business_id']    = $payload['business_id'] > 0 ? $payload['business_id'] : null;
        $payload['start_date']     = !empty($payload['start_date']) ? $payload['start_date'] : null;
        $payload['end_date']       = !empty($payload['end_date']) ? $payload['end_date'] : null;
        $payload['created_at']     = current_time('mysql');
        $payload['updated_at']     = current_time('mysql');

        $inserted = $wpdb->insert(
            $table,
            $payload,
            array('%d','%d','%d','%d','%s','%s','%s','%s','%s','%s','%s','%s')
        );

        if ($inserted === false) {
            return epos_projects_format_response(false, 'Failed to create project.', null, 500);
        }

        $item_id = (int) $wpdb->insert_id;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $item_id),
            ARRAY_A
        );

        return epos_projects_format_response(true, 'Project created successfully.', $item, 201);
    }
}

if (!function_exists('epos_projects_update')) {
    function epos_projects_update(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_projects_table();
        $id      = absint($request['id']);
        $payload = epos_projects_sanitize_payload($request->get_json_params());

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );

        if (!$exists) {
            return epos_projects_format_response(false, 'Project not found.', null, 404);
        }

        if ($payload['title'] === '') {
            return epos_projects_format_response(false, 'The title field is required.', null, 400);
        }

        $payload['client_id']      = $payload['client_id'] > 0 ? $payload['client_id'] : null;
        $payload['opportunity_id'] = $payload['opportunity_id'] > 0 ? $payload['opportunity_id'] : null;
        $payload['contract_id']    = $payload['contract_id'] > 0 ? $payload['contract_id'] : null;
        $payload['business_id']    = $payload['business_id'] > 0 ? $payload['business_id'] : null;
        $payload['start_date']     = !empty($payload['start_date']) ? $payload['start_date'] : null;
        $payload['end_date']       = !empty($payload['end_date']) ? $payload['end_date'] : null;
        $payload['updated_at']     = current_time('mysql');

        $updated = $wpdb->update(
            $table,
            $payload,
            array('id' => $id),
            array('%d','%d','%d','%d','%s','%s','%s','%s','%s','%s','%s'),
            array('%d')
        );

        if ($updated === false) {
            return epos_projects_format_response(false, 'Failed to update project.', null, 500);
        }

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return epos_projects_format_response(true, 'Project updated successfully.', $item);
    }
}

if (!function_exists('epos_register_projects_routes')) {
    function epos_register_projects_routes() {
        register_rest_route(epos_get_rest_namespace(), '/projects', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_projects_list',
                'permission_callback' => 'epos_projects_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'epos_projects_create',
                'permission_callback' => 'epos_projects_api_permission',
            ),
        ));

        register_rest_route(epos_get_rest_namespace(), '/projects/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_projects_get',
                'permission_callback' => 'epos_projects_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'epos_projects_update',
                'permission_callback' => 'epos_projects_api_permission',
            ),
        ));
    }
}
add_action('rest_api_init', 'epos_register_projects_routes');
