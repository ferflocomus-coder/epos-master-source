<?php
/**
 * EPOS Clients API
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_clients_api_permission')) {
    function epos_clients_api_permission() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_clients_table')) {
    function epos_clients_table() {
        return epos_get_table_name('clients');
    }
}

if (!function_exists('epos_clients_sanitize_payload')) {
    function epos_clients_sanitize_payload($data) {
        $data = is_array($data) ? $data : array();

        return array(
            'full_name'      => isset($data['full_name']) ? sanitize_text_field($data['full_name']) : '',
            'email'          => isset($data['email']) ? sanitize_email($data['email']) : '',
            'phone'          => isset($data['phone']) ? sanitize_text_field($data['phone']) : '',
            'address_line_1' => isset($data['address_line_1']) ? sanitize_text_field($data['address_line_1']) : '',
            'address_line_2' => isset($data['address_line_2']) ? sanitize_text_field($data['address_line_2']) : '',
            'city'           => isset($data['city']) ? sanitize_text_field($data['city']) : '',
            'state'          => isset($data['state']) ? sanitize_text_field($data['state']) : '',
            'postal_code'    => isset($data['postal_code']) ? sanitize_text_field($data['postal_code']) : '',
            'source'         => isset($data['source']) ? sanitize_text_field($data['source']) : '',
            'status'         => isset($data['status']) ? sanitize_text_field($data['status']) : 'lead',
        );
    }
}

if (!function_exists('epos_clients_format_response')) {
    function epos_clients_format_response($success, $message, $data = null, $status = 200) {
        return new WP_REST_Response(array(
            'success' => (bool) $success,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }
}

if (!function_exists('epos_clients_list')) {
    function epos_clients_list(WP_REST_Request $request) {
        global $wpdb;

        $table    = epos_clients_table();
        $page     = max(1, absint($request->get_param('page')));
        $per_page = max(1, min(100, absint($request->get_param('per_page')) ?: 20));
        $offset   = ($page - 1) * $per_page;
        $search   = sanitize_text_field((string) $request->get_param('search'));
        $status   = sanitize_text_field((string) $request->get_param('status'));

        $where   = array('1=1');
        $params  = array();

        if ($search !== '') {
            $where[]  = '(full_name LIKE %s OR email LIKE %s OR phone LIKE %s)';
            $like      = '%' . $wpdb->esc_like($search) . '%';
            $params[]  = $like;
            $params[]  = $like;
            $params[]  = $like;
        }

        if ($status !== '') {
            $where[] = 'status = %s';
            $params[] = $status;
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

        return epos_clients_format_response(true, 'Clients retrieved successfully.', array(
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

if (!function_exists('epos_clients_get')) {
    function epos_clients_get(WP_REST_Request $request) {
        global $wpdb;

        $table = epos_clients_table();
        $id    = absint($request['id']);

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$item) {
            return epos_clients_format_response(false, 'Client not found.', null, 404);
        }

        return epos_clients_format_response(true, 'Client retrieved successfully.', $item);
    }
}

if (!function_exists('epos_clients_create')) {
    function epos_clients_create(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_clients_table();
        $payload = epos_clients_sanitize_payload($request->get_json_params());

        if ($payload['full_name'] === '') {
            return epos_clients_format_response(false, 'The full_name field is required.', null, 400);
        }

        $payload['created_at'] = current_time('mysql');
        $payload['updated_at'] = current_time('mysql');

        $inserted = $wpdb->insert(
            $table,
            $payload,
            array('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')
        );

        if ($inserted === false) {
            return epos_clients_format_response(false, 'Failed to create client.', null, 500);
        }

        $client_id = (int) $wpdb->insert_id;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $client_id),
            ARRAY_A
        );

        return epos_clients_format_response(true, 'Client created successfully.', $item, 201);
    }
}

if (!function_exists('epos_clients_update')) {
    function epos_clients_update(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_clients_table();
        $id      = absint($request['id']);
        $payload = epos_clients_sanitize_payload($request->get_json_params());

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );

        if (!$exists) {
            return epos_clients_format_response(false, 'Client not found.', null, 404);
        }

        if ($payload['full_name'] === '') {
            return epos_clients_format_response(false, 'The full_name field is required.', null, 400);
        }

        $payload['updated_at'] = current_time('mysql');

        $updated = $wpdb->update(
            $table,
            $payload,
            array('id' => $id),
            array('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'),
            array('%d')
        );

        if ($updated === false) {
            return epos_clients_format_response(false, 'Failed to update client.', null, 500);
        }

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return epos_clients_format_response(true, 'Client updated successfully.', $item);
    }
}

if (!function_exists('epos_register_clients_routes')) {
    function epos_register_clients_routes() {
        register_rest_route(epos_get_rest_namespace(), '/clients', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_clients_list',
                'permission_callback' => 'epos_clients_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'epos_clients_create',
                'permission_callback' => 'epos_clients_api_permission',
            ),
        ));

        register_rest_route(epos_get_rest_namespace(), '/clients/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_clients_get',
                'permission_callback' => 'epos_clients_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'epos_clients_update',
                'permission_callback' => 'epos_clients_api_permission',
            ),
        ));
    }
}
add_action('rest_api_init', 'epos_register_clients_routes');
