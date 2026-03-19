# CODEX PROMPT - PHASE 5 - PROJECT TASKS API

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the EPOS Project Tasks module database and REST/API snippet.

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
- Do not implement kanban drag/drop behavior yet

Snippet name:
`EPOS Project Tasks API`

Responsibilities:
1. Register the project tasks table migration if it does not exist yet
2. Register REST routes for project tasks
3. Implement list project tasks
4. Implement get project task details
5. Implement create project task
6. Implement update project task
7. Keep everything safe for repeated execution inside WPCode

Base namespace:
`/wp-json/ep/v1/project-tasks`

Required endpoints:
- `GET /project-tasks`
- `GET /project-tasks/{id}`
- `POST /project-tasks`
- `PUT /project-tasks/{id}`

Technical requirements:
- Use `register_rest_route()`
- Use `epos_get_rest_namespace()`
- Use `epos_get_table_name('project_tasks')`
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
  - stage_id
  - assigned_user_id
  - status
  - priority
  - search

Fields in `ep_project_tasks`:
- project_id
- stage_id
- assigned_user_id
- title
- description
- priority
- status
- due_date
- created_at
- updated_at

Output:
- One single PHP snippet
- Paste-ready for WPCode
- No extra explanation inside the file

Expected file path:
`wpcode-snippets/tasks/epos-project-tasks-api.php`
