# CODEX PROMPT - PHASE 7 - SYSTEM USERS API

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the EPOS System Users module REST/API snippet.

Constraints:
- WordPress runtime only
- PHP only
- WPCode snippet compatible
- No plugin structure
- No Composer
- No autoloaders
- Use `$wpdb`
- Use the EPOS Core Bootstrap helpers already created
- Use the existing `ep_system_users` table
- Do not implement frontend UI yet

Snippet name:
`EPOS System Users API`

Responsibilities:
1. Register REST routes for system users
2. Implement list system users
3. Implement get system user details
4. Implement create system user
5. Implement update system user
6. Keep everything safe for repeated execution inside WPCode

Base namespace:
`/wp-json/ep/v1/system-users`

Required endpoints:
- `GET /system-users`
- `GET /system-users/{id}`
- `POST /system-users`
- `PUT /system-users/{id}`

Technical requirements:
- Use `register_rest_route()`
- Use `epos_get_rest_namespace()`
- Use `epos_get_table_name('system_users')`
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
  - role_id
  - status
  - search

Fields in `ep_system_users`:
- wp_user_id
- role_id
- full_name
- email
- phone
- status
- created_at
- updated_at

Output:
- One single PHP snippet
- Paste-ready for WPCode
- No extra explanation inside the file

Expected file path:
`wpcode-snippets/users-roles/epos-system-users-api.php`
