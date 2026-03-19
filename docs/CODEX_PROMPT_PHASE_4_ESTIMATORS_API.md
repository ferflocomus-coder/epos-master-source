# CODEX PROMPT - PHASE 4 - ESTIMATORS API

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the EPOS Estimators module database and REST/API snippet.

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
- Do not implement proposal or contract modules yet

Snippet name:
`EPOS Estimators API`

Responsibilities:
1. Register the estimator table migration if it does not exist yet
2. Register REST routes for estimators
3. Implement list estimators
4. Implement get estimator details
5. Implement create estimator
6. Implement update estimator
7. Keep everything safe for repeated execution inside WPCode

Base namespace:
`/wp-json/ep/v1/estimators`

Required endpoints:
- `GET /estimators`
- `GET /estimators/{id}`
- `POST /estimators`
- `PUT /estimators/{id}`

Technical requirements:
- Use `register_rest_route()`
- Use `epos_get_rest_namespace()`
- Use `epos_get_table_name('estimators')`
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
  - sales_rep_id
  - status
  - search

Fields in `ep_estimators`:
- project_id
- opportunity_id
- address
- roof_type
- material_type
- roof_area
- slope
- estimated_material_cost
- estimated_labor_cost
- total_estimated_price
- sales_rep_id
- status
- created_at
- updated_at

Output:
- One single PHP snippet
- Paste-ready for WPCode
- No extra explanation inside the file

Expected file path:
`wpcode-snippets/estimators/epos-estimators-api.php`
