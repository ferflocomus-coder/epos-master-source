<?php
/**
 * EPOS Leads API
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_leads_api_permission')) {
    function epos_leads_api_permission() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_leads_table')) {
    function epos_leads_table() {
        return epos_get_table_name('leads');
    }
}

if (!function_exists('epos_leads_sanitize_payload')) {
    function epos_leads_sanitize_payload($data) {
        $data = is_array($data) ? $data : array();

        return array(
            'client_id'        => isset($data['client_id']) ? absint($data['client_id']) : 0,
            'lead_source'      => isset($data['lead_source']) ? sanitize_text_field($data['lead_source']) : '',
            'campaign_name'    => isset($data['campaign_name']) ? sanitize_text_field($data['campaign_name']) : '',
            'assigned_user_id' => isset($data['assigned_user_id']) ? absint($data['assigned_user_id']) : 0,
            'status'           => isset($data['status']) ? sanitize_text_field($data['status']) : 'new',
            'notes'            => isset($data['notes']) ? sanitize_textarea_field($data['notes']) : '',
        );
    }
}

if (!function_exists('epos_leads_format_response')) {
    function epos_leads_format_response($success, $message, $data = null, $status = 200) {
        return new WP_REST_Response(array(
            'success' => (bool) $success,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }
}

if (!function_exists('epos_leads_list')) {
    function epos_leads_list(WP_REST_Request $request) {
        global $wpdb;

        $table            = epos_leads_table();
        $page             = max(1, absint($request->get_param('page')));
        $per_page         = max(1, min(100, absint($request->get_param('per_page')) ?: 20));
        $offset           = ($page - 1) * $per_page;
        $search           = sanitize_text_field((string) $request->get_param('search'));
        $status           = sanitize_text_field((string) $request->get_param('status'));
        $assigned_user_id = absint($request->get_param('assigned_user_id'));
        $client_id        = absint($request->get_param('client_id'));

        $where  = array('1=1');
        $params = array();

        if ($search !== '') {
            $where[] = '(lead_source LIKE %s OR campaign_name LIKE %s OR notes LIKE %s)';
            $like = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if ($status !== '') {
            $where[] = 'status = %s';
            $params[] = $status;
        }

        if ($assigned_user_id > 0) {
            $where[] = 'assigned_user_id = %d';
            $params[] = $assigned_user_id;
        }

        if ($client_id > 0) {
            $where[] = 'client_id = %d';
            $params[] = $client_id;
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

        return epos_leads_format_response(true, 'Leads retrieved successfully.', array(
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

if (!function_exists('epos_leads_get')) {
    function epos_leads_get(WP_REST_Request $request) {
        global $wpdb;

        $table = epos_leads_table();
        $id    = absint($request['id']);

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$item) {
            return epos_leads_format_response(false, 'Lead not found.', null, 404);
        }

        return epos_leads_format_response(true, 'Lead retrieved successfully.', $item);
    }
}

if (!function_exists('epos_leads_create')) {
    function epos_leads_create(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_leads_table();
        $payload = epos_leads_sanitize_payload($request->get_json_params());

        $payload['client_id']        = $payload['client_id'] > 0 ? $payload['client_id'] : null;
        $payload['assigned_user_id'] = $payload['assigned_user_id'] > 0 ? $payload['assigned_user_id'] : null;
        $payload['created_at']       = current_time('mysql');
        $payload['updated_at']       = current_time('mysql');

        $inserted = $wpdb->insert(
            $table,
            $payload,
            array('%d','%s','%s','%d','%s','%s','%s','%s')
        );

        if ($inserted === false) {
            return epos_leads_format_response(false, 'Failed to create lead.', null, 500);
        }

        $lead_id = (int) $wpdb->insert_id;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $lead_id),
            ARRAY_A
        );

        return epos_leads_format_response(true, 'Lead created successfully.', $item, 201);
    }
}

if (!function_exists('epos_leads_update')) {
    function epos_leads_update(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_leads_table();
        $id      = absint($request['id']);
        $payload = epos_leads_sanitize_payload($request->get_json_params());

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );

        if (!$exists) {
            return epos_leads_format_response(false, 'Lead not found.', null, 404);
        }

        $payload['client_id']        = $payload['client_id'] > 0 ? $payload['client_id'] : null;
        $payload['assigned_user_id'] = $payload['assigned_user_id'] > 0 ? $payload['assigned_user_id'] : null;
        $payload['updated_at']       = current_time('mysql');

        $updated = $wpdb->update(
            $table,
            $payload,
            array('id' => $id),
            array('%d','%s','%s','%d','%s','%s','%s'),
            array('%d')
        );

        if ($updated === false) {
            return epos_leads_format_response(false, 'Failed to update lead.', null, 500);
        }

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return epos_leads_format_response(true, 'Lead updated successfully.', $item);
    }
}

if (!function_exists('epos_register_leads_routes')) {
    function epos_register_leads_routes() {
        register_rest_route(epos_get_rest_namespace(), '/leads', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_leads_list',
                'permission_callback' => 'epos_leads_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'epos_leads_create',
                'permission_callback' => 'epos_leads_api_permission',
            ),
        ));

        register_rest_route(epos_get_rest_namespace(), '/leads/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_leads_get',
                'permission_callback' => 'epos_leads_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'epos_leads_update',
                'permission_callback' => 'epos_leads_api_permission',
            ),
        ));
    }
}
add_action('rest_api_init', 'epos_register_leads_routes');
