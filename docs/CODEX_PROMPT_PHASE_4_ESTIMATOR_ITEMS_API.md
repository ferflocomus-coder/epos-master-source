# CODEX PROMPT - PHASE 4 - ESTIMATOR ITEMS API

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the EPOS Estimator Items module database and REST/API snippet.

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

Snippet name:
`EPOS Estimator Items API`

Responsibilities:
1. Register the estimator items table migration if it does not exist yet
2. Register REST routes for estimator items
3. Implement list estimator items
4. Implement get estimator item details
5. Implement create estimator item
6. Implement update estimator item
7. Keep everything safe for repeated execution inside WPCode

Base namespace:
`/wp-json/ep/v1/estimator-items`

Required endpoints:
- `GET /estimator-items`
- `GET /estimator-items/{id}`
- `POST /estimator-items`
- `PUT /estimator-items/{id}`

Technical requirements:
- Use `register_rest_route()`
- Use `epos_get_rest_namespace()`
- Use `epos_get_table_name('estimator_items')`
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
  - estimator_id
  - item_type
  - search

Fields in `ep_estimator_items`:
- estimator_id
- item_type
- item_name
- unit
- quantity
- unit_cost
- total_cost
- sort_order
- created_at
- updated_at

Output:
- One single PHP snippet
- Paste-ready for WPCode
- No extra explanation inside the file

Expected file path:
`wpcode-snippets/estimators/epos-estimator-items-api.php`
