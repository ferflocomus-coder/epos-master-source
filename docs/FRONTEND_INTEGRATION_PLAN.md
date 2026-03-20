# FRONTEND INTEGRATION PLAN

## Objective
Connect the approved dashboard UI to the current EPOS backend APIs without changing the approved visual design.

## Visual Rule
The file below remains the visual source of truth:
- `frontend/ui/dashboard.html`

No visual redesign is allowed.

## Integration Approach
The dashboard integration layer must do the following:
- read API data from WordPress REST API
- replace static blocks with live backend data
- keep layout, spacing, colors, and visual hierarchy unchanged
- isolate API calls from UI rendering logic

## Current Available API Endpoints
### Core
- `/wp-json/ep/v1/system/bootstrap-status`

### Business
- `/wp-json/ep/v1/businesses`

### CRM
- `/wp-json/ep/v1/clients`
- `/wp-json/ep/v1/leads`
- `/wp-json/ep/v1/opportunities`

### Sales
- `/wp-json/ep/v1/estimators`
- `/wp-json/ep/v1/estimator-items`
- `/wp-json/ep/v1/proposals`
- `/wp-json/ep/v1/contracts`

### Execution
- `/wp-json/ep/v1/projects`
- `/wp-json/ep/v1/project-stages`
- `/wp-json/ep/v1/project-tasks`
- `/wp-json/ep/v1/installers`

### Finance
- `/wp-json/ep/v1/commissions`

### Documents
- `/wp-json/ep/v1/documents`
- `/wp-json/ep/v1/document-signatures`

### System
- `/wp-json/ep/v1/notifications`
- `/wp-json/ep/v1/roles`
- `/wp-json/ep/v1/system-users`

## Recommended Frontend Structure
Use the existing frontend folders already created:
- `frontend/components/`
- `frontend/pages/`
- `frontend/layout/`
- `frontend/styles/`
- `frontend/services/`
- `frontend/hooks/`

## Recommended Technical Split
### 1. services
Purpose:
- centralize API calls
- define endpoint paths
- define fetch helpers

Recommended files:
- `frontend/services/api-client.js`
- `frontend/services/api-endpoints.js`
- `frontend/services/dashboard-service.js`

### 2. hooks
Purpose:
- data loading state
- error state
- refresh logic

Recommended files:
- `frontend/hooks/useDashboardData.js`
- `frontend/hooks/useProjectsBoard.js`

### 3. components
Purpose:
- isolated render blocks mapped to approved UI sections

Recommended blocks:
- KPI cards block
- pipeline table block
- task list block
- sidebar navigation block
- top header block

### 4. pages
Purpose:
- compose the page using approved dashboard structure

Recommended page:
- `frontend/pages/dashboard.js`

## First Data Bindings Recommended
The first dashboard binding should focus on these blocks only:

### Block A - KPI Summary
Map static KPI cards to live data sources:
- Total Revenue (temporary source can remain placeholder until financial aggregation exists)
- Active Projects → `/projects`
- Pending Signatures → `/document-signatures` or `/contracts`
- New Leads → `/leads`

### Block B - Pipeline Snapshot
Map the current table to live rows from:
- `/opportunities`
- optionally enrich with `/clients` and `/projects`

### Block C - My Tasks
Map the task widget to live rows from:
- `/project-tasks`

## Data Strategy Rule
Do not fetch directly inside raw HTML blocks.

Use a service layer first.

Then bind processed data into the UI.

## Permissions Note
Current backend endpoints use `manage_options`.

This means frontend integration is currently suitable only for administrator-level validation until operational permissions are implemented.

## Safe Integration Sequence
1. create endpoint constants
2. create generic API client helper
3. create dashboard service aggregator
4. create dashboard data hook
5. bind KPI cards
6. bind pipeline snapshot table
7. bind my tasks widget
8. only after that, move to full board interaction

## Not Included Yet
This plan does not yet implement:
- drag and drop for tasks
- operational user authentication
- token/session handling for non-admin users
- real-time notifications
- reports dashboard
