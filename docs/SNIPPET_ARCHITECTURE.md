# SNIPPET_ARCHITECTURE

## 1. Naming Convention
Pattern:

EPOS - Module - Responsibility

Examples:
- EPOS - Core - Bootstrap
- EPOS - CRM - Tables
- EPOS - CRM - REST Routes
- EPOS - Projects - Services

## 2. Repository Structure
- docs/
- frontend/
- frontend/components/
- frontend/hooks/
- frontend/layout/
- frontend/pages/
- frontend/services/
- frontend/styles/
- frontend/ui/
- wpcode-snippets/
- wpcode-snippets/auth/
- wpcode-snippets/business/
- wpcode-snippets/projects/
- wpcode-snippets/stages/
- wpcode-snippets/tasks/
- wpcode-snippets/estimators/
- wpcode-snippets/proposals/
- wpcode-snippets/contracts/
- wpcode-snippets/crm/
- wpcode-snippets/database/
- wpcode-snippets/frontend/
- wpcode-snippets/installers/
- wpcode-snippets/commissions/
- wpcode-snippets/documents/
- wpcode-snippets/notifications/
- wpcode-snippets/reports/
- wpcode-snippets/users-roles/

## 3. Table Naming Rules
Pattern:
wp_prefix + ep_ + table_name

Examples:
- ep_clients
- ep_leads
- ep_opportunities
- ep_projects
- ep_project_tasks

Rules:
- snake_case only
- primary key should be id
- do not reuse WordPress core table names

## 4. REST API Namespace
Base namespace:
/wp-json/ep/v1/

Examples:
- /wp-json/ep/v1/clients
- /wp-json/ep/v1/leads
- /wp-json/ep/v1/opportunities
- /wp-json/ep/v1/estimators
- /wp-json/ep/v1/contracts
- /wp-json/ep/v1/projects
- /wp-json/ep/v1/project-tasks
- /wp-json/ep/v1/installers
- /wp-json/ep/v1/commissions
- /wp-json/ep/v1/documents
- /wp-json/ep/v1/notifications
- /wp-json/ep/v1/system-users
- /wp-json/ep/v1/roles

## 5. Module Boundaries
Each module must be independent.

Each module may contain:
- table creation logic
- service functions
- REST route registration
- validation
- response formatting
- dashboard integration hooks
