# CODEX PROMPT - PHASE 5 - PROJECT STAGES API

Repository: `ferflocomus-coder/epos-master-source`

Task:
Create the EPOS Project Stages module database and REST/API snippet.

Constraints:
- WordPress runtime only
- PHP only
- WPCode snippet compatible
- No plugin structure
- No Composer
- No autoloaders
- Use $wpdb
- Use the EPOS Core Bootstrap helpers already created
- Use migration registration for table creation
- Do not implement frontend UI yet

Snippet name:
EPOS Project Stages API

Responsibilities:
1. Register the project stages table migration if it does not exist yet
2. Register REST routes for project stages
3. Implement list project stages
4. Implement get project stage details
5. Implement create project stage
6. Implement update project stage
7. Keep everything safe for repeated execution inside WPCode

Base namespace:
/wp-json/ep/v1/project-stages

Required endpoints:
- GET /project-stages
- GET /project-stages/{id}
- POST /project-stages
- PUT /project-stages/{id}

Expected file path:
wpcode-snippets/stages/epos-project-stages-api.php
