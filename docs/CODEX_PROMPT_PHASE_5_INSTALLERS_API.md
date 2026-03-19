# CODEX PROMPT - PHASE 5 - INSTALLERS API

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the EPOS Installers module database and REST/API snippet.

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
- Do not implement assignment workflow yet

Snippet name:
`EPOS Installers API`

Responsibilities:
1. Register the installers table migration if it does not exist yet
2. Register REST routes for installers
3. Implement list installers
4. Implement get installer details
5. Implement create installer
6. Implement update installer
7. Keep everything safe for repeated execution inside WPCode

Base namespace:
`/wp-json/ep/v1/installers`

Required endpoints:
- `GET /installers`
- `GET /installers/{id}`
- `POST /installers`
- `PUT /installers/{id}`

Technical requirements:
- Use `register_rest_route()`
- Use `epos_get_rest_namespace()`
- Use `epos_get_table_name('installers')`
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
  - trade_type
  - status
  - search

Fields in `ep_installers`:
- full_name
- company_name
- email
- phone
- trade_type
- status
- notes
- created_at
- updated_at

Output:
- One single PHP snippet
- Paste-ready for WPCode
- No extra explanation inside the file

Expected file path:
`wpcode-snippets/installers/epos-installers-api.php`
