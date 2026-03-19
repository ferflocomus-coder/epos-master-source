<?php
/**
 * EPOS System Users API
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_system_users_api_permission')) {
    function epos_system_users_api_permission() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_system_users_table')) {
    function epos_system_users_table() {
        return epos_get_table_name('system_users');
    }
}

if (!function_exists('epos_system_users_sanitize_payload')) {
    function epos_system_users_sanitize_payload($data) {
        $data = is_array($data) ? $data : array();

        return array(
            'wp_user_id' => isset($data['wp_user_id']) ? absint($data['wp_user_id']) : 0,
            'role_id'    => isset($data['role_id']) ? absint($data['role_id']) : 0,
            'full_name'  => isset($data['full_name']) ? sanitize_text_field($data['full_name']) : '',
            'email'      => isset($data['email']) ? sanitize_email($data['email']) : '',
            'phone'      => isset($data['phone']) ? sanitize_text_field($data['phone']) : '',
            'status'     => isset($data['status']) ? sanitize_text_field($data['status']) : 'active',
        );
    }
}

if (!function_exists('epos_system_users_format_response')) {
    function epos_system_users_format_response($success, $message, $data = null, $status = 200) {
        return new WP_REST_Response(array(
            'success' => (bool) $success,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }
}

if (!function_exists('epos_system_users_list')) {
    function epos_system_users_list(WP_REST_Request $request) {
        global $wpdb;

        $table    = epos_system_users_table();
        $page     = max(1, absint($request->get_param('page')));
        $per_page = max(1, min(100, absint($request->get_param('per_page')) ?: 20));
        $offset   = ($page - 1) * $per_page;
        $search   = sanitize_text_field((string) $request->get_param('search'));
        $status   = sanitize_text_field((string) $request->get_param('status'));
        $role_id  = absint($request->get_param('role_id'));

        $where  = array('1=1');
        $params = array();

        if ($search !== '') {
            $where[] = '(full_name LIKE %s OR email LIKE %s OR phone LIKE %s)';
            $like = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if ($status !== '') {
            $where[] = 'status = %s';
            $params[] = $status;
        }

        if ($role_id > 0) {
            $where[] = 'role_id = %d';
            $params[] = $role_id;
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

        return epos_system_users_format_response(true, 'System users retrieved successfully.', array(
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

if (!function_exists('epos_system_users_get')) {
    function epos_system_users_get(WP_REST_Request $request) {
        global $wpdb;

        $table = epos_system_users_table();
        $id    = absint($request['id']);

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$item) {
            return epos_system_users_format_response(false, 'System user not found.', null, 404);
        }

        return epos_system_users_format_response(true, 'System user retrieved successfully.', $item);
    }
}

if (!function_exists('epos_system_users_create')) {
    function epos_system_users_create(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_system_users_table();
        $payload = epos_system_users_sanitize_payload($request->get_json_params());

        if ($payload['full_name'] === '' || $payload['email'] === '') {
            return epos_system_users_format_response(false, 'The full_name and email fields are required.', null, 400);
        }

        $payload['wp_user_id'] = $payload['wp_user_id'] > 0 ? $payload['wp_user_id'] : null;
        $payload['role_id']    = $payload['role_id'] > 0 ? $payload['role_id'] : null;
        $payload['created_at'] = current_time('mysql');
        $payload['updated_at'] = current_time('mysql');

        $inserted = $wpdb->insert(
            $table,
            $payload,
            array('%d','%d','%s','%s','%s','%s','%s','%s')
        );

        if ($inserted === false) {
            return epos_system_users_format_response(false, 'Failed to create system user.', null, 500);
        }

        $item_id = (int) $wpdb->insert_id;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $item_id),
            ARRAY_A
        );

        return epos_system_users_format_response(true, 'System user created successfully.', $item, 201);
    }
}

if (!function_exists('epos_system_users_update')) {
    function epos_system_users_update(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_system_users_table();
        $id      = absint($request['id']);
        $payload = epos_system_users_sanitize_payload($request->get_json_params());

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );

        if (!$exists) {
            return epos_system_users_format_response(false, 'System user not found.', null, 404);
        }

        if ($payload['full_name'] === '' || $payload['email'] === '') {
            return epos_system_users_format_response(false, 'The full_name and email fields are required.', null, 400);
        }

        $payload['wp_user_id'] = $payload['wp_user_id'] > 0 ? $payload['wp_user_id'] : null;
        $payload['role_id']    = $payload['role_id'] > 0 ? $payload['role_id'] : null;
        $payload['updated_at'] = current_time('mysql');

        $updated = $wpdb->update(
            $table,
            $payload,
            array('id' => $id),
            array('%d','%d','%s','%s','%s','%s','%s'),
            array('%d')
        );

        if ($updated === false) {
            return epos_system_users_format_response(false, 'Failed to update system user.', null, 500);
        }

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return epos_system_users_format_response(true, 'System user updated successfully.', $item);
    }
}

if (!function_exists('epos_register_system_users_routes')) {
    function epos_register_system_users_routes() {
        register_rest_route(epos_get_rest_namespace(), '/system-users', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_system_users_list',
                'permission_callback' => 'epos_system_users_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'epos_system_users_create',
                'permission_callback' => 'epos_system_users_api_permission',
            ),
        ));

        register_rest_route(epos_get_rest_namespace(), '/system-users/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_system_users_get',
                'permission_callback' => 'epos_system_users_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'epos_system_users_update',
                'permission_callback' => 'epos_system_users_api_permission',
            ),
        ));
    }
}
add_action('rest_api_init', 'epos_register_system_users_routes');
