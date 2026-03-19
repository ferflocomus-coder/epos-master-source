# CODEX PROMPT - PHASE 3 - CLIENTS API

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the first operational REST/API snippet for the EPOS Clients module.

Constraints:
- WordPress runtime only
- PHP only
- WPCode snippet compatible
- No plugin structure
- No Composer
- No autoloaders
- Use `$wpdb`
- Use the EPOS Core Bootstrap helpers already created
- Use the `ep_clients` table already defined
- Do not implement frontend UI yet
- Do not implement other modules yet

Snippet name:
`EPOS Clients API`

Responsibilities:
1. Register REST routes for the clients module
2. Implement list clients
3. Implement get client details
4. Implement create client
5. Implement update client
6. Keep everything safe for repeated execution inside WPCode

Base namespace:
`/wp-json/ep/v1/clients`

Required endpoints:
- `GET /clients`
- `GET /clients/{id}`
- `POST /clients`
- `PUT /clients/{id}`

Technical requirements:
- Use `register_rest_route()`
- Use `epos_get_rest_namespace()`
- Use `epos_get_table_name('clients')`
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
  - search
  - status

Fields in `ep_clients`:
- full_name
- email
- phone
- address_line_1
- address_line_2
- city
- state
- postal_code
- source
- status
- created_at
- updated_at

Output:
- One single PHP snippet
- Paste-ready for WPCode
- No extra explanation inside the file

Expected file path:
`wpcode-snippets/crm/epos-clients-api.php`
