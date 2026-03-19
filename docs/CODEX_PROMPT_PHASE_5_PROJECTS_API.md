# CODEX PROMPT - PHASE 5 - PROJECTS API

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the EPOS Projects module database and REST/API snippet.

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
- Do not implement tasks module yet

Snippet name:
`EPOS Projects API`

Responsibilities:
1. Register the projects table migration if it does not exist yet
2. Register REST routes for projects
3. Implement list projects
4. Implement get project details
5. Implement create project
6. Implement update project
7. Keep everything safe for repeated execution inside WPCode

Base namespace:
`/wp-json/ep/v1/projects`

Required endpoints:
- `GET /projects`
- `GET /projects/{id}`
- `POST /projects`
- `PUT /projects/{id}`

Technical requirements:
- Use `register_rest_route()`
- Use `epos_get_rest_namespace()`
- Use `epos_get_table_name('projects')`
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
  - opportunity_id
  - contract_id
  - business_id
  - status
  - search

Fields in `ep_projects`:
- client_id
- opportunity_id
- contract_id
- business_id
- title
- description
- address
- status
- start_date
- end_date
- created_at
- updated_at

Output:
- One single PHP snippet
- Paste-ready for WPCode
- No extra explanation inside the file

Expected file path:
`wpcode-snippets/projects/epos-projects-api.php`
