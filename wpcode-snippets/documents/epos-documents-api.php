<?php
/**
 * EPOS Documents API
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('epos_documents_register_table_migration')) {
    function epos_documents_register_table_migration() {
        epos_register_migration('epos_db_012_documents', function () {
            global $wpdb;

            $table_name = epos_get_table_name('documents');
            $charset    = $wpdb->get_charset_collate();

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $sql = "CREATE TABLE {$table_name} (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                entity_type VARCHAR(100) NOT NULL,
                entity_id BIGINT UNSIGNED NULL,
                document_type VARCHAR(100) NULL,
                title VARCHAR(190) NOT NULL,
                file_url VARCHAR(255) NULL,
                status VARCHAR(50) NOT NULL DEFAULT 'draft',
                created_by BIGINT UNSIGNED NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_entity_type (entity_type),
                KEY idx_entity_id (entity_id),
                KEY idx_document_type (document_type),
                KEY idx_status (status),
                KEY idx_created_by (created_by)
            ) {$charset};";

            dbDelta($sql);
        });
    }
}
add_action('init', 'epos_documents_register_table_migration', 12);

if (!function_exists('epos_documents_api_permission')) {
    function epos_documents_api_permission() {
        return current_user_can('manage_options');
    }
}

if (!function_exists('epos_documents_table')) {
    function epos_documents_table() {
        return epos_get_table_name('documents');
    }
}

if (!function_exists('epos_documents_sanitize_payload')) {
    function epos_documents_sanitize_payload($data) {
        $data = is_array($data) ? $data : array();

        return array(
            'entity_type'   => isset($data['entity_type']) ? sanitize_text_field($data['entity_type']) : '',
            'entity_id'     => isset($data['entity_id']) ? absint($data['entity_id']) : 0,
            'document_type' => isset($data['document_type']) ? sanitize_text_field($data['document_type']) : '',
            'title'         => isset($data['title']) ? sanitize_text_field($data['title']) : '',
            'file_url'      => isset($data['file_url']) ? esc_url_raw($data['file_url']) : '',
            'status'        => isset($data['status']) ? sanitize_text_field($data['status']) : 'draft',
            'created_by'    => isset($data['created_by']) ? absint($data['created_by']) : 0,
        );
    }
}

if (!function_exists('epos_documents_format_response')) {
    function epos_documents_format_response($success, $message, $data = null, $status = 200) {
        return new WP_REST_Response(array(
            'success' => (bool) $success,
            'message' => $message,
            'data'    => $data,
        ), $status);
    }
}

if (!function_exists('epos_documents_list')) {
    function epos_documents_list(WP_REST_Request $request) {
        global $wpdb;

        $table         = epos_documents_table();
        $page          = max(1, absint($request->get_param('page')));
        $per_page      = max(1, min(100, absint($request->get_param('per_page')) ?: 20));
        $offset        = ($page - 1) * $per_page;
        $entity_type   = sanitize_text_field((string) $request->get_param('entity_type'));
        $entity_id     = absint($request->get_param('entity_id'));
        $document_type = sanitize_text_field((string) $request->get_param('document_type'));
        $status        = sanitize_text_field((string) $request->get_param('status'));
        $created_by    = absint($request->get_param('created_by'));

        $where  = array('1=1');
        $params = array();

        if ($entity_type !== '') {
            $where[] = 'entity_type = %s';
            $params[] = $entity_type;
        }

        if ($entity_id > 0) {
            $where[] = 'entity_id = %d';
            $params[] = $entity_id;
        }

        if ($document_type !== '') {
            $where[] = 'document_type = %s';
            $params[] = $document_type;
        }

        if ($status !== '') {
            $where[] = 'status = %s';
            $params[] = $status;
        }

        if ($created_by > 0) {
            $where[] = 'created_by = %d';
            $params[] = $created_by;
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

        return epos_documents_format_response(true, 'Documents retrieved successfully.', array(
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

if (!function_exists('epos_documents_get')) {
    function epos_documents_get(WP_REST_Request $request) {
        global $wpdb;

        $table = epos_documents_table();
        $id    = absint($request['id']);

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$item) {
            return epos_documents_format_response(false, 'Document not found.', null, 404);
        }

        return epos_documents_format_response(true, 'Document retrieved successfully.', $item);
    }
}

if (!function_exists('epos_documents_create')) {
    function epos_documents_create(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_documents_table();
        $payload = epos_documents_sanitize_payload($request->get_json_params());

        if ($payload['entity_type'] === '' || $payload['title'] === '') {
            return epos_documents_format_response(false, 'The entity_type and title fields are required.', null, 400);
        }

        $payload['entity_id']   = $payload['entity_id'] > 0 ? $payload['entity_id'] : null;
        $payload['created_by']  = $payload['created_by'] > 0 ? $payload['created_by'] : null;
        $payload['created_at']  = current_time('mysql');
        $payload['updated_at']  = current_time('mysql');

        $inserted = $wpdb->insert(
            $table,
            $payload,
            array('%s','%d','%s','%s','%s','%s','%d','%s','%s')
        );

        if ($inserted === false) {
            return epos_documents_format_response(false, 'Failed to create document.', null, 500);
        }

        $item_id = (int) $wpdb->insert_id;
        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $item_id),
            ARRAY_A
        );

        return epos_documents_format_response(true, 'Document created successfully.', $item, 201);
    }
}

if (!function_exists('epos_documents_update')) {
    function epos_documents_update(WP_REST_Request $request) {
        global $wpdb;

        $table   = epos_documents_table();
        $id      = absint($request['id']);
        $payload = epos_documents_sanitize_payload($request->get_json_params());

        $exists = $wpdb->get_var(
            $wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id)
        );

        if (!$exists) {
            return epos_documents_format_response(false, 'Document not found.', null, 404);
        }

        if ($payload['entity_type'] === '' || $payload['title'] === '') {
            return epos_documents_format_response(false, 'The entity_type and title fields are required.', null, 400);
        }

        $payload['entity_id']   = $payload['entity_id'] > 0 ? $payload['entity_id'] : null;
        $payload['created_by']  = $payload['created_by'] > 0 ? $payload['created_by'] : null;
        $payload['updated_at']  = current_time('mysql');

        $updated = $wpdb->update(
            $table,
            $payload,
            array('id' => $id),
            array('%s','%d','%s','%s','%s','%s','%d','%s'),
            array('%d')
        );

        if ($updated === false) {
            return epos_documents_format_response(false, 'Failed to update document.', null, 500);
        }

        $item = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id),
            ARRAY_A
        );

        return epos_documents_format_response(true, 'Document updated successfully.', $item);
    }
}

if (!function_exists('epos_register_documents_routes')) {
    function epos_register_documents_routes() {
        register_rest_route(epos_get_rest_namespace(), '/documents', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_documents_list',
                'permission_callback' => 'epos_documents_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'epos_documents_create',
                'permission_callback' => 'epos_documents_api_permission',
            ),
        ));

        register_rest_route(epos_get_rest_namespace(), '/documents/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'epos_documents_get',
                'permission_callback' => 'epos_documents_api_permission',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'epos_documents_update',
                'permission_callback' => 'epos_documents_api_permission',
            ),
        ));
    }
}
add_action('rest_api_init', 'epos_register_documents_routes');
