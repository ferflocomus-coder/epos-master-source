# CODEX PROMPT - PHASE 2 - DATABASE BASE

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the first database initialization snippet for Evolution Power OS (EPOS).

Constraints:
- WordPress runtime only
- PHP only
- WPCode snippet compatible
- No plugin structure
- No Composer
- No autoloaders
- Use `$wpdb`
- Use the EPOS Core Bootstrap helpers already created
- Do not implement business logic yet
- Do not implement REST endpoints yet

Snippet name:
`EPOS Database Base Tables`

Responsibilities:
1. Register the base table migrations for the system
2. Create the foundational tables only
3. Use `dbDelta()` safely
4. Respect the data model documentation already present in `/docs`

Scope for this phase:
Create only these foundational tables:
- ep_roles
- ep_system_users
- ep_clients
- ep_leads
- ep_opportunities

Technical requirements:
- Use `epos_register_migration()`
- Use `epos_get_table_name()`
- Use `dbDelta()`
- Include:
  - primary keys
  - indexes for foreign key style columns
  - timestamps
  - status fields where defined
- Do not add real SQL foreign key constraints yet
- Make the snippet safe for repeated execution in WPCode
- Do not create seed data

Output:
- One single PHP snippet
- Paste-ready for WPCode
- No extra explanation inside the file

Expected file path:
`wpcode-snippets/database/epos-database-base-tables.php`
