<?php
/**
 * EPOS Estimators API
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_estimators_register_table_migration')) {
    function epos_estimators_register_table_migration() {
        epos_register_migration('epos_db_006_estimators', function () {
            global $wpdb;

            $table_name = epos_get_table_name('estimators');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                project_id BIGINT UNSIGNED NULL,
                opportunity_id BIGINT UNSIGNED NULL,
                address VARCHAR(255) NULL,
                roof_type VARCHAR(100) NULL,
                material_type VARCHAR(100) NULL,
                roof_area DECIMAL(12,2) NULL DEFAULT 0.00,
                slope VARCHAR(50) NULL,
                estimated_material_cost DECIMAL(15,2) NULL DEFAULT 0.00,
                estimated_labor_cost DECIMAL(15,2) NULL DEFAULT 0.00,
                total_estimated_price DECIMAL(15,2) NULL DEFAULT 0.00,
                sales_rep_id BIGINT UNSIGNED NULL,
                status VARCHAR(50) NOT NULL DEFAULT 'draft',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_project_id (project_id),
                KEY idx_opportunity_id (opportunity_id),
                KEY idx_sales_rep_id (sales_rep_id),
                KEY idx_status (status),
                KEY idx_roof_type (roof_type),
                KEY idx_material_type (material_type)
            ) {$charset};";

            dbDelta($sql);
        });
    }
}
add_action('init', 'epos_estimators_register_table_migration', 6);

if (!function_exists('epos_estimators_api_permission')) {
    function epos_estimators_api_permission() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_estimators_table')) {
    function epos_estimators_table() {
        return epos_get_table_name('estimators');
    }
}

if (!function_exists('epos_estimators_sanitize_payload')) {
    function epos_estimators_sanitize_payload($data) {
        $data = is_array($data) ? $data : array();

        return array(
            'project_id'               => isset($data['project_id']) ? absint($data['project_id']) : 0,
            'opportunity_id'           => isset($data['opportunity_id']) ? absint($data['opportunity_id']) : 0,
            'address'                  => isset($data['address']) ? sanitize_text_field($data['address']) : '',
            'roof_type'                => isset($data['roof_type']) ? sanitize_text_field($data['roof_type']) : '',
            'material_type'            => isset($data['material_type']) ? sanitize_text_field($data['material_type']) : '',
            'roof_area'                => isset($data['roof_area']) ? (float) $data['roof_area'] : 0,
            'slope'                    => isset($data['slope']) ? sanitize_text_field($data['slope']) : '',
            'estimated_material_cost'  => isset($data['estimated_material_cost']) ? (float) $data['estimated_material_cost'] : 0,
            'estimated_labor_cost'     => isset($data['estimated_labor_cost']) ? (float) $data['estimated_labor_cost'] : 0,
            'total_estimated_price'    => isset($data['total_estimated_price']) ? (float) $data['total_estimated_price'] : 0,
            'sales_rep_id'             => isset($data['sales_rep_id']) ? absint($data['sales_rep_id']) : 0,
            'status'                   => isset($data['status']) ? sanitize_text_field($data['status']) : 'draft',
        );
    }
}

if (!function_exists('epos_estimators_format_response')) {
    function epos_estimators_format_response($success, $message, $data = null, $status = 200) {
        return new WP_REST_Response(array(
            'success' => (bool) $success,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }
}

if (!function_exists('epos_estimators_list')) {
    function epos_estimators_list(WP_REST_Request $request) {
        global $wpdb;

        $table        = epos_estimators_table();
        $page         = max(1, absint($request->get_param('page')));
        $per_page     = max(1, min(100, absint($request->get_param('per_page')) ?: 20));
        $offset       = ($page - 1) * $per_page;
        $search       = sanitize_text_field((string) $request->get_param('search'));
        $status       = sanitize_text_field((string) $request->get_param('status'));
        $project_id   = absint($request->get_param('project_id'));
        $sales_rep_id = absint($request->get_param('sales_rep_id'));

        $where  = array('1=1');
        $params = array();

        if ($search !== '') {
            $where[] = '(address LIKE %s OR roof_type LIKE %s OR material_type LIKE %s)';
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

        if ($sales_rep_id > 0) {
            $where[] = 'sales_rep_id = %d';
            $params[] = $sales_rep_id;
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

        return epos_estimators_format_response(true, 'Estimators retrieved successfully.', array(
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

if (!function_exists('epos_estimators_get')) {
    function epos_estimators_get(WP_REST_Request $request) {
        global $wpdb;

        $table = epos_estimators_table();
        $id    = absint($request['id']);

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$item) {
            return epos_estimators_format_response(false, 'Estimator not found.', null, 404);
        }

        return epos_estimators_format_response(true, 'Estimator retrieved successfully.', $item);
    }
}

if (!function_exists('epos_estimators_create')) {
    function epos_estimators_create(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_estimators_table();
        $payload = epos_estimators_sanitize_payload($request->get_json_params());

        $payload['project_id']              = $payload['project_id'] > 0 ? $payload['project_id'] : null;
        $payload['opportunity_id']          = $payload['opportunity_id'] > 0 ? $payload['opportunity_id'] : null;
        $payload['sales_rep_id']            = $payload['sales_rep_id'] > 0 ? $payload['sales_rep_id'] : null;
        $payload['created_at']              = current_time('mysql');
        $payload['updated_at']              = current_time('mysql');

        $inserted = $wpdb->insert(
            $table,
            $payload,
            array('%d','%d','%s','%s','%s','%f','%s','%f','%f','%f','%d','%s','%s','%s')
        );

        if ($inserted === false) {
            return epos_estimators_format_response(false, 'Failed to create estimator.', null, 500);
        }

        $item_id = (int) $wpdb->insert_id;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $item_id),
            ARRAY_A
        );

        return epos_estimators_format_response(true, 'Estimator created successfully.', $item, 201);
    }
}

if (!function_exists('epos_estimators_update')) {
    function epos_estimators_update(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_estimators_table();
        $id      = absint($request['id']);
        $payload = epos_estimators_sanitize_payload($request->get_json_params());

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );

        if (!$exists) {
            return epos_estimators_format_response(false, 'Estimator not found.', null, 404);
        }

        $payload['project_id']              = $payload['project_id'] > 0 ? $payload['project_id'] : null;
        $payload['opportunity_id']          = $payload['opportunity_id'] > 0 ? $payload['opportunity_id'] : null;
        $payload['sales_rep_id']            = $payload['sales_rep_id'] > 0 ? $payload['sales_rep_id'] : null;
        $payload['updated_at']              = current_time('mysql');

        $updated = $wpdb->update(
            $table,
            $payload,
            array('id' => $id),
            array('%d','%d','%s','%s','%s','%f','%s','%f','%f','%f','%d','%s','%s'),
            array('%d')
        );

        if ($updated === false) {
            return epos_estimators_format_response(false, 'Failed to update estimator.', null, 500);
        }

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return epos_estimators_format_response(true, 'Estimator updated successfully.', $item);
    }
}

if (!function_exists('epos_register_estimators_routes')) {
    function epos_register_estimators_routes() {
        register_rest_route(epos_get_rest_namespace(), '/estimators', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_estimators_list',
                'permission_callback' => 'epos_estimators_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'epos_estimators_create',
                'permission_callback' => 'epos_estimators_api_permission',
            ),
        ));

        register_rest_route(epos_get_rest_namespace(), '/estimators/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_estimators_get',
                'permission_callback' => 'epos_estimators_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'epos_estimators_update',
                'permission_callback' => 'epos_estimators_api_permission',
            ),
        ));
    }
}
add_action('rest_api_init', 'epos_register_estimators_routes');
