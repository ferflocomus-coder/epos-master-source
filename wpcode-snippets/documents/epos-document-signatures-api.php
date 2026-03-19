<?php
/**
 * EPOS Document Signatures API
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_document_signatures_register_table_migration')) {
    function epos_document_signatures_register_table_migration() {
        epos_register_migration('epos_db_016_document_signatures', function () {
            global $wpdb;

            $table_name = epos_get_table_name('document_signatures');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                document_id BIGINT UNSIGNED NULL,
                signer_name VARCHAR(190) NULL,
                signer_email VARCHAR(190) NULL,
                signer_role VARCHAR(100) NULL,
                signed_at DATETIME NULL,
                signature_url VARCHAR(255) NULL,
                status VARCHAR(50) NOT NULL DEFAULT 'pending',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_document_id (document_id),
                KEY idx_signer_email (signer_email),
                KEY idx_signer_role (signer_role),
                KEY idx_status (status)
            ) {$charset};";

            dbDelta($sql);
        });
    }
}
add_action('init', 'epos_document_signatures_register_table_migration', 16);

if (!function_exists('epos_document_signatures_api_permission')) {
    function epos_document_signatures_api_permission() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_document_signatures_table')) {
    function epos_document_signatures_table() {
        return epos_get_table_name('document_signatures');
    }
}

if (!function_exists('epos_document_signatures_sanitize_payload')) {
    function epos_document_signatures_sanitize_payload($data) {
        $data = is_array($data) ? $data : array();

        return array(
            'document_id'    => isset($data['document_id']) ? absint($data['document_id']) : 0,
            'signer_name'    => isset($data['signer_name']) ? sanitize_text_field($data['signer_name']) : '',
            'signer_email'   => isset($data['signer_email']) ? sanitize_email($data['signer_email']) : '',
            'signer_role'    => isset($data['signer_role']) ? sanitize_text_field($data['signer_role']) : '',
            'signed_at'      => isset($data['signed_at']) ? sanitize_text_field($data['signed_at']) : null,
            'signature_url'  => isset($data['signature_url']) ? esc_url_raw($data['signature_url']) : '',
            'status'         => isset($data['status']) ? sanitize_text_field($data['status']) : 'pending',
        );
    }
}

if (!function_exists('epos_document_signatures_format_response')) {
    function epos_document_signatures_format_response($success, $message, $data = null, $status = 200) {
        return new WP_REST_Response(array(
            'success' => (bool) $success,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }
}

if (!function_exists('epos_document_signatures_list')) {
    function epos_document_signatures_list(WP_REST_Request $request) {
        global $wpdb;

        $table        = epos_document_signatures_table();
        $page         = max(1, absint($request->get_param('page')));
        $per_page     = max(1, min(100, absint($request->get_param('per_page')) ?: 20));
        $offset       = ($page - 1) * $per_page;
        $document_id  = absint($request->get_param('document_id'));
        $signer_email = sanitize_text_field((string) $request->get_param('signer_email'));
        $signer_role  = sanitize_text_field((string) $request->get_param('signer_role'));
        $status       = sanitize_text_field((string) $request->get_param('status'));

        $where  = array('1=1');
        $params = array();

        if ($document_id > 0) {
            $where[] = 'document_id = %d';
            $params[] = $document_id;
        }

        if ($signer_email !== '') {
            $where[] = 'signer_email = %s';
            $params[] = $signer_email;
        }

        if ($signer_role !== '') {
            $where[] = 'signer_role = %s';
            $params[] = $signer_role;
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

        return epos_document_signatures_format_response(true, 'Document signatures retrieved successfully.', array(
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

if (!function_exists('epos_document_signatures_get')) {
    function epos_document_signatures_get(WP_REST_Request $request) {
        global $wpdb;

        $table = epos_document_signatures_table();
        $id    = absint($request['id']);

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$item) {
            return epos_document_signatures_format_response(false, 'Document signature not found.', null, 404);
        }

        return epos_document_signatures_format_response(true, 'Document signature retrieved successfully.', $item);
    }
}

if (!function_exists('epos_document_signatures_create')) {
    function epos_document_signatures_create(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_document_signatures_table();
        $payload = epos_document_signatures_sanitize_payload($request->get_json_params());

        $payload['document_id'] = $payload['document_id'] > 0 ? $payload['document_id'] : null;
        $payload['signed_at']   = !empty($payload['signed_at']) ? $payload['signed_at'] : null;
        $payload['created_at']  = current_time('mysql');
        $payload['updated_at']  = current_time('mysql');

        $inserted = $wpdb->insert(
            $table,
            $payload,
            array('%d','%s','%s','%s','%s','%s','%s','%s','%s')
        );

        if ($inserted === false) {
            return epos_document_signatures_format_response(false, 'Failed to create document signature.', null, 500);
        }

        $item_id = (int) $wpdb->insert_id;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $item_id),
            ARRAY_A
        );

        return epos_document_signatures_format_response(true, 'Document signature created successfully.', $item, 201);
    }
}

if (!function_exists('epos_document_signatures_update')) {
    function epos_document_signatures_update(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_document_signatures_table();
        $id      = absint($request['id']);
        $payload = epos_document_signatures_sanitize_payload($request->get_json_params());

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );

        if (!$exists) {
            return epos_document_signatures_format_response(false, 'Document signature not found.', null, 404);
        }

        $payload['document_id'] = $payload['document_id'] > 0 ? $payload['document_id'] : null;
        $payload['signed_at']   = !empty($payload['signed_at']) ? $payload['signed_at'] : null;
        $payload['updated_at']  = current_time('mysql');

        $updated = $wpdb->update(
            $table,
            $payload,
            array('id' => $id),
            array('%d','%s','%s','%s','%s','%s','%s','%s'),
            array('%d')
        );

        if ($updated === false) {
            return epos_document_signatures_format_response(false, 'Failed to update document signature.', null, 500);
        }

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return epos_document_signatures_format_response(true, 'Document signature updated successfully.', $item);
    }
}

if (!function_exists('epos_register_document_signatures_routes')) {
    function epos_register_document_signatures_routes() {
        register_rest_route(epos_get_rest_namespace(), '/document-signatures', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_document_signatures_list',
                'permission_callback' => 'epos_document_signatures_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'epos_document_signatures_create',
                'permission_callback' => 'epos_document_signatures_api_permission',
            ),
        ));

        register_rest_route(epos_get_rest_namespace(), '/document-signatures/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_document_signatures_get',
                'permission_callback' => 'epos_document_signatures_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'epos_document_signatures_update',
                'permission_callback' => 'epos_document_signatures_api_permission',
            ),
        ));
    }
}
add_action('rest_api_init', 'epos_register_document_signatures_routes');
