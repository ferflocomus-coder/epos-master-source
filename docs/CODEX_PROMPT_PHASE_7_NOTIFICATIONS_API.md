# CODEX PROMPT - PHASE 7 - NOTIFICATIONS API

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the EPOS Notifications module database and REST/API snippet.

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
- Do not implement real-time delivery yet

Snippet name:
`EPOS Notifications API`

Responsibilities:
1. Register the notifications table migration if it does not exist yet
2. Register REST routes for notifications
3. Implement list notifications
4. Implement get notification details
5. Implement create notification
6. Implement update notification
7. Keep everything safe for repeated execution inside WPCode

Base namespace:
`/wp-json/ep/v1/notifications`

Required endpoints:
- `GET /notifications`
- `GET /notifications/{id}`
- `POST /notifications`
- `PUT /notifications/{id}`

Technical requirements:
- Use `register_rest_route()`
- Use `epos_get_rest_namespace()`
- Use `epos_get_table_name('notifications')`
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
  - user_id
  - is_read
  - type
  - entity_type
  - entity_id

Fields in `ep_notifications`:
- user_id
- type
- title
- message
- entity_type
- entity_id
- is_read
- created_at
- updated_at

Output:
- One single PHP snippet
- Paste-ready for WPCode
- No extra explanation inside the file

Expected file path:
`wpcode-snippets/notifications/epos-notifications-api.php`
