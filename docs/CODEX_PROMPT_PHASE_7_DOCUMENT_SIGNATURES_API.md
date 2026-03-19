# CODEX PROMPT - PHASE 7 - DOCUMENT SIGNATURES API

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the EPOS Document Signatures module database and REST/API snippet.

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
- Do not implement e-signature workflow yet

Snippet name:
`EPOS Document Signatures API`

Responsibilities:
1. Register the document signatures table migration if it does not exist yet
2. Register REST routes for document signatures
3. Implement list document signatures
4. Implement get document signature details
5. Implement create document signature
6. Implement update document signature
7. Keep everything safe for repeated execution inside WPCode

Base namespace:
`/wp-json/ep/v1/document-signatures`

Required endpoints:
- `GET /document-signatures`
- `GET /document-signatures/{id}`
- `POST /document-signatures`
- `PUT /document-signatures/{id}`

Technical requirements:
- Use `register_rest_route()`
- Use `epos_get_rest_namespace()`
- Use `epos_get_table_name('document_signatures')`
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
  - document_id
  - signer_email
  - status
  - signer_role

Fields in `ep_document_signatures`:
- document_id
- signer_name
- signer_email
- signer_role
- signed_at
- signature_url
- status
- created_at
- updated_at

Output:
- One single PHP snippet
- Paste-ready for WPCode
- No extra explanation inside the file

Expected file path:
`wpcode-snippets/documents/epos-document-signatures-api.php`
