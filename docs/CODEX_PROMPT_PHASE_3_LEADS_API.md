# CODEX PROMPT - PHASE 3 - LEADS API

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the EPOS Leads module REST/API snippet.

Constraints:
- WordPress runtime only
- PHP only
- WPCode snippet compatible
- No plugin structure
- No Composer
- No autoloaders
- Use `$wpdb`
- Use the EPOS Core Bootstrap helpers already created
- Use the `ep_leads` table already defined
- Do not implement frontend UI yet
- Do not implement other modules yet

Snippet name:
`EPOS Leads API`

Responsibilities:
1. Register REST routes for the leads module
2. Implement list leads
3. Implement get lead details
4. Implement create lead
5. Implement update lead
6. Keep everything safe for repeated execution inside WPCode

Base namespace:
`/wp-json/ep/v1/leads`

Required endpoints:
- `GET /leads`
- `GET /leads/{id}`
- `POST /leads`
- `PUT /leads/{id}`

Technical requirements:
- Use `register_rest_route()`
- Use `epos_get_rest_namespace()`
- Use `epos_get_table_name('leads')`
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
  - assigned_user_id
  - client_id

Fields in `ep_leads`:
- client_id
- lead_source
- campaign_name
- assigned_user_id
- status
- notes
- created_at
- updated_at

Output:
- One single PHP snippet
- Paste-ready for WPCode
- No extra explanation inside the file

Expected file path:
`wpcode-snippets/crm/epos-leads-api.php`
