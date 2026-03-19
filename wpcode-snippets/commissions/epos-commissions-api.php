<?php
/**
 * EPOS Commissions API
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_commissions_register_table_migration')) {
    function epos_commissions_register_table_migration() {
        epos_register_migration('epos_db_011_commissions', function () {
            global $wpdb;

            $table_name = epos_get_table_name('commissions');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                project_id BIGINT UNSIGNED NULL,
                contract_id BIGINT UNSIGNED NULL,
                installer_id BIGINT UNSIGNED NULL,
                user_id BIGINT UNSIGNED NULL,
                commission_type VARCHAR(100) NULL,
                base_amount DECIMAL(15,2) NULL DEFAULT 0.00,
                commission_amount DECIMAL(15,2) NULL DEFAULT 0.00,
                status VARCHAR(50) NOT NULL DEFAULT 'pending',
                approved_at DATETIME NULL,
                paid_at DATETIME NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_project_id (project_id),
                KEY idx_contract_id (contract_id),
                KEY idx_installer_id (installer_id),
                KEY idx_user_id (user_id),
                KEY idx_commission_type (commission_type),
                KEY idx_status (status)
            ) {$charset};";

            dbDelta($sql);
        });
    }
}
add_action('init', 'epos_commissions_register_table_migration', 11);

if (!function_exists('epos_commissions_api_permission')) {
    function epos_commissions_api_permission() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_commissions_table')) {
    function epos_commissions_table() {
        return epos_get_table_name('commissions');
    }
}

if (!function_exists('epos_commissions_sanitize_payload')) {
    function epos_commissions_sanitize_payload($data) {
        $data = is_array($data) ? $data : array();

        return array(
            'project_id'         => isset($data['project_id']) ? absint($data['project_id']) : 0,
            'contract_id'        => isset($data['contract_id']) ? absint($data['contract_id']) : 0,
            'installer_id'       => isset($data['installer_id']) ? absint($data['installer_id']) : 0,
            'user_id'            => isset($data['user_id']) ? absint($data['user_id']) : 0,
            'commission_type'    => isset($data['commission_type']) ? sanitize_text_field($data['commission_type']) : '',
            'base_amount'        => isset($data['base_amount']) ? (float) $data['base_amount'] : 0,
            'commission_amount'  => isset($data['commission_amount']) ? (float) $data['commission_amount'] : 0,
            'status'             => isset($data['status']) ? sanitize_text_field($data['status']) : 'pending',
            'approved_at'        => isset($data['approved_at']) ? sanitize_text_field($data['approved_at']) : null,
            'paid_at'            => isset($data['paid_at']) ? sanitize_text_field($data['paid_at']) : null,
        );
    }
}

if (!function_exists('epos_commissions_format_response')) {
    function epos_commissions_format_response($success, $message, $data = null, $status = 200) {
        return new WP_REST_Response(array(
            'success' => (bool) $success,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }
}

if (!function_exists('epos_commissions_list')) {
    function epos_commissions_list(WP_REST_Request $request) {
        global $wpdb;

        $table           = epos_commissions_table();
        $page            = max(1, absint($request->get_param('page')));
        $per_page        = max(1, min(100, absint($request->get_param('per_page')) ?: 20));
        $offset          = ($page - 1) * $per_page;
        $status          = sanitize_text_field((string) $request->get_param('status'));
        $commission_type = sanitize_text_field((string) $request->get_param('commission_type'));
        $project_id      = absint($request->get_param('project_id'));
        $contract_id     = absint($request->get_param('contract_id'));
        $installer_id    = absint($request->get_param('installer_id'));
        $user_id         = absint($request->get_param('user_id'));

        $where  = array('1=1');
        $params = array();

        if ($status !== '') {
            $where[] = 'status = %s';
            $params[] = $status;
        }

        if ($commission_type !== '') {
            $where[] = 'commission_type = %s';
            $params[] = $commission_type;
        }

        if ($project_id > 0) {
            $where[] = 'project_id = %d';
            $params[] = $project_id;
        }

        if ($contract_id > 0) {
            $where[] = 'contract_id = %d';
            $params[] = $contract_id;
        }

        if ($installer_id > 0) {
            $where[] = 'installer_id = %d';
            $params[] = $installer_id;
        }

        if ($user_id > 0) {
            $where[] = 'user_id = %d';
            $params[] = $user_id;
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

        return epos_commissions_format_response(true, 'Commissions retrieved successfully.', array(
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

if (!function_exists('epos_commissions_get')) {
    function epos_commissions_get(WP_REST_Request $request) {
        global $wpdb;

        $table = epos_commissions_table();
        $id    = absint($request['id']);

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$item) {
            return epos_commissions_format_response(false, 'Commission not found.', null, 404);
        }

        return epos_commissions_format_response(true, 'Commission retrieved successfully.', $item);
    }
}

if (!function_exists('epos_commissions_create')) {
    function epos_commissions_create(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_commissions_table();
        $payload = epos_commissions_sanitize_payload($request->get_json_params());

        $payload['project_id']     = $payload['project_id'] > 0 ? $payload['project_id'] : null;
        $payload['contract_id']    = $payload['contract_id'] > 0 ? $payload['contract_id'] : null;
        $payload['installer_id']   = $payload['installer_id'] > 0 ? $payload['installer_id'] : null;
        $payload['user_id']        = $payload['user_id'] > 0 ? $payload['user_id'] : null;
        $payload['approved_at']    = !empty($payload['approved_at']) ? $payload['approved_at'] : null;
        $payload['paid_at']        = !empty($payload['paid_at']) ? $payload['paid_at'] : null;
        $payload['created_at']     = current_time('mysql');
        $payload['updated_at']     = current_time('mysql');

        $inserted = $wpdb->insert(
            $table,
            $payload,
            array('%d','%d','%d','%d','%s','%f','%f','%s','%s','%s','%s','%s')
        );

        if ($inserted === false) {
            return epos_commissions_format_response(false, 'Failed to create commission.', null, 500);
        }

        $item_id = (int) $wpdb->insert_id;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $item_id),
            ARRAY_A
        );

        return epos_commissions_format_response(true, 'Commission created successfully.', $item, 201);
    }
}

if (!function_exists('epos_commissions_update')) {
    function epos_commissions_update(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_commissions_table();
        $id      = absint($request['id']);
        $payload = epos_commissions_sanitize_payload($request->get_json_params());

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );

        if (!$exists) {
            return epos_commissions_format_response(false, 'Commission not found.', null, 404);
        }

        $payload['project_id']     = $payload['project_id'] > 0 ? $payload['project_id'] : null;
        $payload['contract_id']    = $payload['contract_id'] > 0 ? $payload['contract_id'] : null;
        $payload['installer_id']   = $payload['installer_id'] > 0 ? $payload['installer_id'] : null;
        $payload['user_id']        = $payload['user_id'] > 0 ? $payload['user_id'] : null;
        $payload['approved_at']    = !empty($payload['approved_at']) ? $payload['approved_at'] : null;
        $payload['paid_at']        = !empty($payload['paid_at']) ? $payload['paid_at'] : null;
        $payload['updated_at']     = current_time('mysql');

        $updated = $wpdb->update(
            $table,
            $payload,
            array('id' => $id),
            array('%d','%d','%d','%d','%s','%f','%f','%s','%s','%s','%s'),
            array('%d')
        );

        if ($updated === false) {
            return epos_commissions_format_response(false, 'Failed to update commission.', null, 500);
        }

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return epos_commissions_format_response(true, 'Commission updated successfully.', $item);
    }
}

if (!function_exists('epos_register_commissions_routes')) {
    function epos_register_commissions_routes() {
        register_rest_route(epos_get_rest_namespace(), '/commissions', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_commissions_list',
                'permission_callback' => 'epos_commissions_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'epos_commissions_create',
                'permission_callback' => 'epos_commissions_api_permission',
            ),
        ));

        register_rest_route(epos_get_rest_namespace(), '/commissions/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_commissions_get',
                'permission_callback' => 'epos_commissions_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'epos_commissions_update',
                'permission_callback' => 'epos_commissions_api_permission',
            ),
        ));
    }
}
add_action('rest_api_init', 'epos_register_commissions_routes');
