# CODEX PROMPT - PHASE 6 - COMMISSIONS API

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the EPOS Commissions module database and REST/API snippet.

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
- Do not implement advanced payout logic yet

Snippet name:
`EPOS Commissions API`

Responsibilities:
1. Register the commissions table migration if it does not exist yet
2. Register REST routes for commissions
3. Implement list commissions
4. Implement get commission details
5. Implement create commission
6. Implement update commission
7. Keep everything safe for repeated execution inside WPCode

Base namespace:
`/wp-json/ep/v1/commissions`

Required endpoints:
- `GET /commissions`
- `GET /commissions/{id}`
- `POST /commissions`
- `PUT /commissions/{id}`

Technical requirements:
- Use `register_rest_route()`
- Use `epos_get_rest_namespace()`
- Use `epos_get_table_name('commissions')`
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
  - project_id
  - contract_id
  - installer_id
  - user_id
  - status
  - commission_type

Fields in `ep_commissions`:
- project_id
- contract_id
- installer_id
- user_id
- commission_type
- base_amount
- commission_amount
- status
- approved_at
- paid_at
- created_at
- updated_at

Output:
- One single PHP snippet
- Paste-ready for WPCode
- No extra explanation inside the file

Expected file path:
`wpcode-snippets/commissions/epos-commissions-api.php`
