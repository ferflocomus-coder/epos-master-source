<?php
/**
 * EPOS Contracts API
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_contracts_register_table_migration')) {
    function epos_contracts_register_table_migration() {
        epos_register_migration('epos_db_007_contracts', function () {
            global $wpdb;

            $table_name = epos_get_table_name('contracts');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                opportunity_id BIGINT UNSIGNED NULL,
                estimator_id BIGINT UNSIGNED NULL,
                project_id BIGINT UNSIGNED NULL,
                contract_number VARCHAR(150) NULL,
                client_name VARCHAR(190) NULL,
                client_email VARCHAR(190) NULL,
                contract_amount DECIMAL(15,2) NULL DEFAULT 0.00,
                status VARCHAR(50) NOT NULL DEFAULT 'draft',
                contract_url VARCHAR(255) NULL,
                signed_at DATETIME NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_opportunity_id (opportunity_id),
                KEY idx_estimator_id (estimator_id),
                KEY idx_project_id (project_id),
                KEY idx_contract_number (contract_number),
                KEY idx_status (status),
                KEY idx_client_email (client_email)
            ) {$charset};";

            dbDelta($sql);
        });
    }
}
add_action('init', 'epos_contracts_register_table_migration', 7);

if (!function_exists('epos_contracts_api_permission')) {
    function epos_contracts_api_permission() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_contracts_table')) {
    function epos_contracts_table() {
        return epos_get_table_name('contracts');
    }
}

if (!function_exists('epos_contracts_sanitize_payload')) {
    function epos_contracts_sanitize_payload($data) {
        $data = is_array($data) ? $data : array();

        return array(
            'opportunity_id'   => isset($data['opportunity_id']) ? absint($data['opportunity_id']) : 0,
            'estimator_id'     => isset($data['estimator_id']) ? absint($data['estimator_id']) : 0,
            'project_id'       => isset($data['project_id']) ? absint($data['project_id']) : 0,
            'contract_number'  => isset($data['contract_number']) ? sanitize_text_field($data['contract_number']) : '',
            'client_name'      => isset($data['client_name']) ? sanitize_text_field($data['client_name']) : '',
            'client_email'     => isset($data['client_email']) ? sanitize_email($data['client_email']) : '',
            'contract_amount'  => isset($data['contract_amount']) ? (float) $data['contract_amount'] : 0,
            'status'           => isset($data['status']) ? sanitize_text_field($data['status']) : 'draft',
            'contract_url'     => isset($data['contract_url']) ? esc_url_raw($data['contract_url']) : '',
            'signed_at'        => isset($data['signed_at']) ? sanitize_text_field($data['signed_at']) : null,
        );
    }
}

if (!function_exists('epos_contracts_format_response')) {
    function epos_contracts_format_response($success, $message, $data = null, $status = 200) {
        return new WP_REST_Response(array(
            'success' => (bool) $success,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }
}

if (!function_exists('epos_contracts_list')) {
    function epos_contracts_list(WP_REST_Request $request) {
        global $wpdb;

        $table          = epos_contracts_table();
        $page           = max(1, absint($request->get_param('page')));
        $per_page       = max(1, min(100, absint($request->get_param('per_page')) ?: 20));
        $offset         = ($page - 1) * $per_page;
        $search         = sanitize_text_field((string) $request->get_param('search'));
        $status         = sanitize_text_field((string) $request->get_param('status'));
        $project_id     = absint($request->get_param('project_id'));
        $opportunity_id = absint($request->get_param('opportunity_id'));
        $estimator_id   = absint($request->get_param('estimator_id'));

        $where  = array('1=1');
        $params = array();

        if ($search !== '') {
            $where[] = '(contract_number LIKE %s OR client_name LIKE %s OR client_email LIKE %s)';
            $like = '%' . $wpdb->esc_like($search) . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        if ($status !== '') {
            $where[] = 'status = %s';
            $params[] = $status;
        }

        if ($project_id > 0) {
            $where[] = 'project_id = %d';
            $params[] = $project_id;
        }

        if ($opportunity_id > 0) {
            $where[] = 'opportunity_id = %d';
            $params[] = $opportunity_id;
        }

        if ($estimator_id > 0) {
            $where[] = 'estimator_id = %d';
            $params[] = $estimator_id;
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

        return epos_contracts_format_response(true, 'Contracts retrieved successfully.', array(
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

if (!function_exists('epos_contracts_get')) {
    function epos_contracts_get(WP_REST_Request $request) {
        global $wpdb;

        $table = epos_contracts_table();
        $id    = absint($request['id']);

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$item) {
            return epos_contracts_format_response(false, 'Contract not found.', null, 404);
        }

        return epos_contracts_format_response(true, 'Contract retrieved successfully.', $item);
    }
}

if (!function_exists('epos_contracts_create')) {
    function epos_contracts_create(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_contracts_table();
        $payload = epos_contracts_sanitize_payload($request->get_json_params());

        $payload['opportunity_id'] = $payload['opportunity_id'] > 0 ? $payload['opportunity_id'] : null;
        $payload['estimator_id']   = $payload['estimator_id'] > 0 ? $payload['estimator_id'] : null;
        $payload['project_id']     = $payload['project_id'] > 0 ? $payload['project_id'] : null;
        $payload['signed_at']      = !empty($payload['signed_at']) ? $payload['signed_at'] : null;
        $payload['created_at']     = current_time('mysql');
        $payload['updated_at']     = current_time('mysql');

        $inserted = $wpdb->insert(
            $table,
            $payload,
            array('%d','%d','%d','%s','%s','%s','%f','%s','%s','%s','%s','%s')
        );

        if ($inserted === false) {
            return epos_contracts_format_response(false, 'Failed to create contract.', null, 500);
        }

        $item_id = (int) $wpdb->insert_id;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $item_id),
            ARRAY_A
        );

        return epos_contracts_format_response(true, 'Contract created successfully.', $item, 201);
    }
}

if (!function_exists('epos_contracts_update')) {
    function epos_contracts_update(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_contracts_table();
        $id      = absint($request['id']);
        $payload = epos_contracts_sanitize_payload($request->get_json_params());

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );

        if (!$exists) {
            return epos_contracts_format_response(false, 'Contract not found.', null, 404);
        }

        $payload['opportunity_id'] = $payload['opportunity_id'] > 0 ? $payload['opportunity_id'] : null;
        $payload['estimator_id']   = $payload['estimator_id'] > 0 ? $payload['estimator_id'] : null;
        $payload['project_id']     = $payload['project_id'] > 0 ? $payload['project_id'] : null;
        $payload['signed_at']      = !empty($payload['signed_at']) ? $payload['signed_at'] : null;
        $payload['updated_at']     = current_time('mysql');

        $updated = $wpdb->update(
            $table,
            $payload,
            array('id' => $id),
            array('%d','%d','%d','%s','%s','%s','%f','%s','%s','%s','%s'),
            array('%d')
        );

        if ($updated === false) {
            return epos_contracts_format_response(false, 'Failed to update contract.', null, 500);
        }

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return epos_contracts_format_response(true, 'Contract updated successfully.', $item);
    }
}

if (!function_exists('epos_register_contracts_routes')) {
    function epos_register_contracts_routes() {
        register_rest_route(epos_get_rest_namespace(), '/contracts', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_contracts_list',
                'permission_callback' => 'epos_contracts_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'epos_contracts_create',
                'permission_callback' => 'epos_contracts_api_permission',
            ),
        ));

        register_rest_route(epos_get_rest_namespace(), '/contracts/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_contracts_get',
                'permission_callback' => 'epos_contracts_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'epos_contracts_update',
                'permission_callback' => 'epos_contracts_api_permission',
            ),
        ));
    }
}
add_action('rest_api_init', 'epos_register_contracts_routes');
