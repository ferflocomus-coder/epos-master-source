<?php
/**
 * EPOS Opportunities API
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_opportunities_api_permission')) {
    function epos_opportunities_api_permission() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_opportunities_table')) {
    function epos_opportunities_table() {
        return epos_get_table_name('opportunities');
    }
}

if (!function_exists('epos_opportunities_sanitize_payload')) {
    function epos_opportunities_sanitize_payload($data) {
        $data = is_array($data) ? $data : array();

        return array(
            'client_id'        => isset($data['client_id']) ? absint($data['client_id']) : 0,
            'lead_id'          => isset($data['lead_id']) ? absint($data['lead_id']) : 0,
            'business_id'      => isset($data['business_id']) ? absint($data['business_id']) : 0,
            'title'            => isset($data['title']) ? sanitize_text_field($data['title']) : '',
            'description'      => isset($data['description']) ? sanitize_textarea_field($data['description']) : '',
            'pipeline_stage'   => isset($data['pipeline_stage']) ? sanitize_text_field($data['pipeline_stage']) : '',
            'status'           => isset($data['status']) ? sanitize_text_field($data['status']) : 'open',
            'estimated_value'  => isset($data['estimated_value']) ? (float) $data['estimated_value'] : 0,
            'assigned_user_id' => isset($data['assigned_user_id']) ? absint($data['assigned_user_id']) : 0,
        );
    }
}

if (!function_exists('epos_opportunities_format_response')) {
    function epos_opportunities_format_response($success, $message, $data = null, $status = 200) {
        return new WP_REST_Response(array(
            'success' => (bool) $success,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }
}

if (!function_exists('epos_opportunities_list')) {
    function epos_opportunities_list(WP_REST_Request $request) {
        global $wpdb;

        $table            = epos_opportunities_table();
        $page             = max(1, absint($request->get_param('page')));
        $per_page         = max(1, min(100, absint($request->get_param('per_page')) ?: 20));
        $offset           = ($page - 1) * $per_page;
        $search           = sanitize_text_field((string) $request->get_param('search'));
        $status           = sanitize_text_field((string) $request->get_param('status'));
        $pipeline_stage   = sanitize_text_field((string) $request->get_param('pipeline_stage'));
        $client_id        = absint($request->get_param('client_id'));
        $lead_id          = absint($request->get_param('lead_id'));
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

        if ($pipeline_stage !== '') {
            $where[] = 'pipeline_stage = %s';
            $params[] = $pipeline_stage;
        }

        if ($client_id > 0) {
            $where[] = 'client_id = %d';
            $params[] = $client_id;
        }

        if ($lead_id > 0) {
            $where[] = 'lead_id = %d';
            $params[] = $lead_id;
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

        return epos_opportunities_format_response(true, 'Opportunities retrieved successfully.', array(
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

if (!function_exists('epos_opportunities_get')) {
    function epos_opportunities_get(WP_REST_Request $request) {
        global $wpdb;

        $table = epos_opportunities_table();
        $id    = absint($request['id']);

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$item) {
            return epos_opportunities_format_response(false, 'Opportunity not found.', null, 404);
        }

        return epos_opportunities_format_response(true, 'Opportunity retrieved successfully.', $item);
    }
}

if (!function_exists('epos_opportunities_create')) {
    function epos_opportunities_create(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_opportunities_table();
        $payload = epos_opportunities_sanitize_payload($request->get_json_params());

        if ($payload['title'] === '') {
            return epos_opportunities_format_response(false, 'The title field is required.', null, 400);
        }

        $payload['client_id']        = $payload['client_id'] > 0 ? $payload['client_id'] : null;
        $payload['lead_id']          = $payload['lead_id'] > 0 ? $payload['lead_id'] : null;
        $payload['business_id']      = $payload['business_id'] > 0 ? $payload['business_id'] : null;
        $payload['assigned_user_id'] = $payload['assigned_user_id'] > 0 ? $payload['assigned_user_id'] : null;
        $payload['created_at']       = current_time('mysql');
        $payload['updated_at']       = current_time('mysql');

        $inserted = $wpdb->insert(
            $table,
            $payload,
            array('%d','%d','%d','%s','%s','%s','%s','%f','%d','%s','%s')
        );

        if ($inserted === false) {
            return epos_opportunities_format_response(false, 'Failed to create opportunity.', null, 500);
        }

        $item_id = (int) $wpdb->insert_id;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $item_id),
            ARRAY_A
        );

        return epos_opportunities_format_response(true, 'Opportunity created successfully.', $item, 201);
    }
}

if (!function_exists('epos_opportunities_update')) {
    function epos_opportunities_update(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_opportunities_table();
        $id      = absint($request['id']);
        $payload = epos_opportunities_sanitize_payload($request->get_json_params());

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );

        if (!$exists) {
            return epos_opportunities_format_response(false, 'Opportunity not found.', null, 404);
        }

        if ($payload['title'] === '') {
            return epos_opportunities_format_response(false, 'The title field is required.', null, 400);
        }

        $payload['client_id']        = $payload['client_id'] > 0 ? $payload['client_id'] : null;
        $payload['lead_id']          = $payload['lead_id'] > 0 ? $payload['lead_id'] : null;
        $payload['business_id']      = $payload['business_id'] > 0 ? $payload['business_id'] : null;
        $payload['assigned_user_id'] = $payload['assigned_user_id'] > 0 ? $payload['assigned_user_id'] : null;
        $payload['updated_at']       = current_time('mysql');

        $updated = $wpdb->update(
            $table,
            $payload,
            array('id' => $id),
            array('%d','%d','%d','%s','%s','%s','%s','%f','%d','%s'),
            array('%d')
        );

        if ($updated === false) {
            return epos_opportunities_format_response(false, 'Failed to update opportunity.', null, 500);
        }

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return epos_opportunities_format_response(true, 'Opportunity updated successfully.', $item);
    }
}

if (!function_exists('epos_register_opportunities_routes')) {
    function epos_register_opportunities_routes() {
        register_rest_route(epos_get_rest_namespace(), '/opportunities', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_opportunities_list',
                'permission_callback' => 'epos_opportunities_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'epos_opportunities_create',
                'permission_callback' => 'epos_opportunities_api_permission',
            ),
        ));

        register_rest_route(epos_get_rest_namespace(), '/opportunities/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_opportunities_get',
                'permission_callback' => 'epos_opportunities_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'epos_opportunities_update',
                'permission_callback' => 'epos_opportunities_api_permission',
            ),
        ));
    }
}
add_action('rest_api_init', 'epos_register_opportunities_routes');
