# WPCODE ACTIVATION ORDER - FULL CURRENT STATE

This document defines the activation order for all EPOS snippets currently present in the repository.

## Goal
Activate the full current backend foundation inside WordPress using WPCode, in a safe order, without breaking dependencies.

## General Rule
All snippets must be created in WPCode as:
- PHP Snippet
- Auto Insert: Run Everywhere
- Status: Active

## Activation Order

### 1. Core Bootstrap
File:
- `wpcode-snippets/epos-core-bootstrap.php`

Purpose:
- base namespace
- helper functions
- migration system
- bootstrap status endpoint

### 2. Database Base Tables
File:
- `wpcode-snippets/database/epos-database-base-tables.php`

Purpose:
- foundational tables
- base migrations for initial entities

### 3. Business
File:
- `wpcode-snippets/business/epos-business-api.php`

### 4. Clients
File:
- `wpcode-snippets/crm/epos-clients-api.php`

### 5. Leads
File:
- `wpcode-snippets/crm/epos-leads-api.php`

### 6. Opportunities
File:
- `wpcode-snippets/crm/epos-opportunities-api.php`

### 7. Estimators
File:
- `wpcode-snippets/estimators/epos-estimators-api.php`

### 8. Estimator Items
File:
- `wpcode-snippets/estimators/epos-estimator-items-api.php`

### 9. Proposals
File:
- `wpcode-snippets/proposals/epos-proposals-api.php`

### 10. Contracts
File:
- `wpcode-snippets/contracts/epos-contracts-api.php`

### 11. Projects
File:
- `wpcode-snippets/projects/epos-projects-api.php`

### 12. Project Stages
File:
- `wpcode-snippets/stages/epos-project-stages-api.php`

### 13. Project Tasks
File:
- `wpcode-snippets/tasks/epos-project-tasks-api.php`

### 14. Installers
File:
- `wpcode-snippets/installers/epos-installers-api.php`

### 15. Commissions
File:
- `wpcode-snippets/commissions/epos-commissions-api.php`

### 16. Documents
File:
- `wpcode-snippets/documents/epos-documents-api.php`

### 17. Document Signatures
File:
- `wpcode-snippets/documents/epos-document-signatures-api.php`

### 18. Notifications
File:
- `wpcode-snippets/notifications/epos-notifications-api.php`

### 19. Roles
File:
- `wpcode-snippets/users-roles/epos-roles-api.php`

### 20. System Users
File:
- `wpcode-snippets/users-roles/epos-system-users-api.php`

## Recommended Execution Sequence in WordPress
1. Activate the core bootstrap snippet
2. Activate the database base tables snippet
3. Refresh WordPress once
4. Activate the remaining snippets in the exact order above
5. Refresh WordPress again after the last snippet is active

## Validation Endpoints
After activation, validate these endpoints while logged in as an administrator.

### Bootstrap
- `GET /wp-json/ep/v1/system/bootstrap-status`

### Business
- `GET /wp-json/ep/v1/businesses`

### CRM
- `GET /wp-json/ep/v1/clients`
- `GET /wp-json/ep/v1/leads`
- `GET /wp-json/ep/v1/opportunities`

### Sales
- `GET /wp-json/ep/v1/estimators`
- `GET /wp-json/ep/v1/estimator-items`
- `GET /wp-json/ep/v1/proposals`
- `GET /wp-json/ep/v1/contracts`

### Execution
- `GET /wp-json/ep/v1/projects`
- `GET /wp-json/ep/v1/project-stages`
- `GET /wp-json/ep/v1/project-tasks`
- `GET /wp-json/ep/v1/installers`

### Finance
- `GET /wp-json/ep/v1/commissions`

### Documents
- `GET /wp-json/ep/v1/documents`
- `GET /wp-json/ep/v1/document-signatures`

### System
- `GET /wp-json/ep/v1/notifications`
- `GET /wp-json/ep/v1/roles`
- `GET /wp-json/ep/v1/system-users`

## Tables Expected After Activation
The following tables should exist with the active WordPress prefix:
- `ep_roles`
- `ep_system_users`
- `ep_clients`
- `ep_leads`
- `ep_opportunities`
- `ep_businesses`
- `ep_estimators`
- `ep_estimator_items`
- `ep_proposals`
- `ep_contracts`
- `ep_projects`
- `ep_project_stages`
- `ep_project_tasks`
- `ep_installers`
- `ep_commissions`
- `ep_documents`
- `ep_document_signatures`
- `ep_notifications`

## Current Access Model
All routes currently require `manage_options`.

This is temporary.

Operational user permissions are not implemented yet.

## Pending Major Areas
Still pending after this activation layer:
- frontend dashboard integration to live APIs
- operational authentication flow
- granular permissions model
- reports module
- dashboard data binding
- route testing pack
- cleanup of temporary repo files
