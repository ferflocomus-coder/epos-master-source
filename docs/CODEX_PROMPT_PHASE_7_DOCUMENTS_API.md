# CODEX PROMPT - PHASE 7 - DOCUMENTS API

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the EPOS Documents module database and REST/API snippet.

Constraints:
- WordPress runtime only
- PHP only
- WPCode snippet compatible
- No plugin structure
- No Composer
- No autoloaders
- Use `$wpdb`
- Use the EPOS Core Bootstrap helpers already created
- Use migration registration for table creation
- Do not implement frontend UI yet
- Do not implement signature workflow yet

Snippet name:
`EPOS Documents API`

Responsibilities:
1. Register the documents table migration if it does not exist yet
2. Register REST routes for documents
3. Implement list documents
4. Implement get document details
5. Implement create document
6. Implement update document
7. Keep everything safe for repeated execution inside WPCode

Base namespace:
`/wp-json/ep/v1/documents`

Required endpoints:
- `GET /documents`
- `GET /documents/{id}`
- `POST /documents`
- `PUT /documents/{id}`

Technical requirements:
- Use `register_rest_route()`
- Use `epos_get_rest_namespace()`
- Use `epos_get_table_name('documents')`
- Sanitize input properly
- Return standard JSON responses with:
  - success
  - message
  - data
- Restrict access with a permission callback using `manage_options` for now
- Use `$wpdb->insert`, `$wpdb->update`, and `$wpdb->get_results` / `$wpdb->get_row`
- Support basic filters in list route:
  - page
  - per_page
  - entity_type
  - entity_id
  - document_type
  - status
  - created_by

Fields in `ep_documents`:
- entity_type
- entity_id
- document_type
- title
- file_url
- status
- created_by
- created_at
- updated_at

Output:
- One single PHP snippet
- Paste-ready for WPCode
- No extra explanation inside the file

Expected file path:
`wpcode-snippets/documents/epos-documents-api.php`
