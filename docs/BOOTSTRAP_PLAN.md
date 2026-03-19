# BOOTSTRAP_PLAN

## Phase 1 - Core Bootstrap Snippet
Create the core bootstrap snippet first.

Responsibilities:
- register REST namespace ep/v1
- register shared helper functions
- implement database migration runner
- prepare shared initialization hooks for all modules

Expected snippet:
- EPOS - Core - Bootstrap

## Phase 2 - Database Tables Initialization
Create the custom database tables in a controlled order.

Recommended order:
1. ep_roles
2. ep_system_users
3. ep_clients
4. ep_leads
5. ep_opportunities
6. ep_estimators
7. ep_estimator_items
8. ep_contracts
9. ep_projects
10. ep_project_tasks
11. ep_installers
12. ep_commissions
13. ep_documents
14. ep_document_signatures
15. ep_notifications

Goal:
- establish the full base schema before business logic is enabled

## Phase 3 - Core Modules
Implement the first operational modules:
- clients
- leads
- opportunities

Each module should include:
- table migration logic if needed
- service functions
- REST endpoints
- validation

## Phase 4 - Sales Modules
Implement sales-related modules:
- estimators
- estimator items
- contracts

Goal:
- support pre-sale calculations and contract generation workflows

## Phase 5 - Execution Modules
Implement delivery and execution modules:
- projects
- project tasks
- installers

Goal:
- support production tracking after sale and contract conversion

## Phase 6 - Finance Modules
Implement finance support modules:
- commissions

Goal:
- track payout logic for installers, closers, or internal users

## Phase 7 - System Modules
Implement platform-level modules:
- users
- roles
- notifications
- documents
- document signatures

Goal:
- complete security, communication, and document lifecycle support

## Phase 8 - Frontend Integration
Integrate backend modules into the approved dashboard UI.

Frontend responsibilities:
- consume WordPress REST API endpoints
- preserve the approved UI design exactly
- replace static dashboard data with live API data
- keep UI logic separate from storage and service logic

## Phase 9 - Stabilization
After the modules are connected:
- validate routes
- validate permissions
- validate migration order
- validate data relationships
- validate dashboard integration
