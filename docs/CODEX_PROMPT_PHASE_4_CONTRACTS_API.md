# CODEX PROMPT - PHASE 4 - CONTRACTS API

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the EPOS Contracts module database and REST/API snippet.

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
- Do not implement proposal module yet

Snippet name:
`EPOS Contracts API`

Responsibilities:
1. Register the contracts table migration if it does not exist yet
2. Register REST routes for contracts
3. Implement list contracts
4. Implement get contract details
5. Implement create contract
6. Implement update contract
7. Keep everything safe for repeated execution inside WPCode

Base namespace:
`/wp-json/ep/v1/contracts`

Required endpoints:
- `GET /contracts`
- `GET /contracts/{id}`
- `POST /contracts`
- `PUT /contracts/{id}`

Technical requirements:
- Use `register_rest_route()`
- Use `epos_get_rest_namespace()`
- Use `epos_get_table_name('contracts')`
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
  - opportunity_id
  - estimator_id
  - status
  - search

Fields in `ep_contracts`:
- opportunity_id
- estimator_id
- project_id
- contract_number
- client_name
- client_email
- contract_amount
- status
- contract_url
- signed_at
- created_at
- updated_at

Output:
- One single PHP snippet
- Paste-ready for WPCode
- No extra explanation inside the file

Expected file path:
`wpcode-snippets/contracts/epos-contracts-api.php`
