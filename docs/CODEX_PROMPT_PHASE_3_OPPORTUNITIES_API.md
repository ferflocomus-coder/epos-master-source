# CODEX PROMPT - PHASE 3 - OPPORTUNITIES API

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the EPOS Opportunities module REST/API snippet.

Constraints:
- WordPress runtime only
- PHP only
- WPCode snippet compatible
- No plugin structure
- No Composer
- No autoloaders
- Use `$wpdb`
- Use the EPOS Core Bootstrap helpers already created
- Use the `ep_opportunities` table already defined
- Do not implement frontend UI yet
- Do not implement other modules yet

Snippet name:
`EPOS Opportunities API`

Responsibilities:
1. Register REST routes for the opportunities module
2. Implement list opportunities
3. Implement get opportunity details
4. Implement create opportunity
5. Implement update opportunity
6. Keep everything safe for repeated execution inside WPCode

Base namespace:
`/wp-json/ep/v1/opportunities`

Required endpoints:
- `GET /opportunities`
- `GET /opportunities/{id}`
- `POST /opportunities`
- `PUT /opportunities/{id}`

Technical requirements:
- Use `register_rest_route()`
- Use `epos_get_rest_namespace()`
- Use `epos_get_table_name('opportunities')`
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
  - client_id
  - lead_id
  - assigned_user_id
  - pipeline_stage
  - status
  - search

Fields in `ep_opportunities`:
- client_id
- lead_id
- business_id
- title
- description
- pipeline_stage
- status
- estimated_value
- assigned_user_id
- created_at
- updated_at

Output:
- One single PHP snippet
- Paste-ready for WPCode
- No extra explanation inside the file

Expected file path:
`wpcode-snippets/crm/epos-opportunities-api.php`
