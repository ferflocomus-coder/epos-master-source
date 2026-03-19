# CODEX PROMPT - PHASE 1 - EPOS CORE BOOTSTRAP

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the first WPCode-compatible bootstrap snippet for Evolution Power OS (EPOS).

Constraints:
- WordPress runtime only
- PHP only
- WPCode snippet compatible
- No plugin structure
- No Composer
- No autoloaders
- No external backend
- Use custom tables via `$wpdb` later, but do not create business tables yet
- Do not implement additional modules yet

Snippet name:
`EPOS Core Bootstrap`

Responsibilities:
1. Define the base REST namespace: `ep/v1`
2. Define reusable helper functions for all future modules
3. Implement a simple migration registry and migration runner
4. Prepare the system for future module initialization
5. Keep everything safe for repeated execution inside WPCode

Technical requirements:
- Use `function_exists()` guards
- Use a global migration registry array
- Provide helper functions such as:
  - `epos_get_rest_namespace()`
  - `epos_get_table_name($table)`
  - `epos_register_migration($key, $callback)`
  - `epos_run_migrations()`
- Use a WordPress option to track executed migrations
- Hook migration execution into `init`
- Add a minimal REST route under `/wp-json/ep/v1/system/bootstrap-status`
- The route should return a JSON response with:
  - success
  - namespace
  - executed_migrations_count
  - pending_migrations_count
  - timestamp

Output:
- One single PHP snippet
- Paste-ready for WPCode
- No classes unless absolutely necessary
- No business modules yet

Expected file path:
`wpcode-snippets/epos-core-bootstrap.php`
