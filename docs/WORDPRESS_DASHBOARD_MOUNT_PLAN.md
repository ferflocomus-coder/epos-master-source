# WORDPRESS DASHBOARD MOUNT PLAN

## Objective
Mount the approved EPOS dashboard inside WordPress using WPCode snippets and a WordPress page.

## Mandatory Rule
The dashboard must run inside WordPress.

It must not be treated as a separate frontend application.

## Approved Visual Source
The visual reference remains:
- `frontend/ui/dashboard.html`

This file is a design reference only.

The real implementation must be rendered from WordPress through WPCode.

## Correct Mounting Model
### Runtime
- WordPress page
- WPCode shortcode snippet
- internal WordPress REST API calls
- custom tables through existing EPOS snippets

### Delivery Model
1. Create a WPCode snippet that registers a shortcode
2. Create a WordPress page for the dashboard
3. Place the shortcode into that page
4. The shortcode outputs the approved dashboard markup
5. The dashboard JavaScript fetches data from `/wp-json/ep/v1/...`
6. Static placeholders are replaced with live data

## Recommended Shortcode
Suggested shortcode:
- `[epos_dashboard]`

## First Implementation Scope
The first shortcode version should do only this:
- render the dashboard shell
- keep the approved layout structure
- include data hooks for:
  - active projects
  - leads count
  - opportunities list
  - project tasks list
- not implement drag and drop yet
- not implement role-based frontend restrictions yet

## Recommended WordPress Flow
### Step 1
Create shortcode snippet:
- registers `[epos_dashboard]`
- outputs wrapper HTML
- enqueues inline CSS/JS only as needed

### Step 2
Create WordPress page:
- title: EPOS Dashboard
- slug: `epos-dashboard`
- content: `[epos_dashboard]`

### Step 3
Bind first live data blocks:
- KPI cards
- pipeline table
- task list

## Data Sources for First Bindings
### KPI Cards
- Active Projects → `/wp-json/ep/v1/projects`
- New Leads → `/wp-json/ep/v1/leads`
- Pending Signatures → `/wp-json/ep/v1/document-signatures`

### Pipeline Snapshot
- `/wp-json/ep/v1/opportunities`

### My Tasks
- `/wp-json/ep/v1/project-tasks`

## Technical Rule
Do not create a React app.
Do not create a separate frontend deployment.
Do not create a plugin.

The dashboard must be mounted directly from WordPress through a shortcode/snippet.

## Recommended Implementation Order
1. Create shortcode bootstrap snippet for dashboard mount
2. Render dashboard shell from shortcode
3. Inject minimal JS fetch layer inside shortcode output
4. Replace first static KPI values with live values
5. Replace pipeline rows with live rows
6. Replace task widget rows with live rows
7. Then continue with deeper interactions

## Pending Later
- login/session UX for operational users
- fine-grained permissions
- drag and drop board actions
- inline editing
- notifications live refresh
- reports UI
