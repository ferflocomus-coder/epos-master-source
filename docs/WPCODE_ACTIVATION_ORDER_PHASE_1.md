# WPCODE ACTIVATION ORDER - PHASE 1

This document defines the activation order for the first EPOS bootstrap layer inside WPCode.

## Goal
Enable the minimum EPOS foundation in WordPress so the following can be tested:
- core bootstrap
- database base tables
- clients API
- leads API
- opportunities API

## Required Snippets
Activate these snippets in this exact order:

### 1. EPOS Core Bootstrap
File:
- `wpcode-snippets/epos-core-bootstrap.php`

Purpose:
- defines the base REST namespace
- defines helper functions
- registers the migration system
- exposes bootstrap status endpoint

### 2. EPOS Database Base Tables
File:
- `wpcode-snippets/database/epos-database-base-tables.php`

Purpose:
- registers base table migrations
- creates foundational EPOS tables

### 3. EPOS Clients API
File:
- `wpcode-snippets/crm/epos-clients-api.php`

Purpose:
- registers clients REST routes
- supports create, update, list, and detail operations

### 4. EPOS Leads API
File:
- `wpcode-snippets/crm/epos-leads-api.php`

Purpose:
- registers leads REST routes
- supports create, update, list, and detail operations

### 5. EPOS Opportunities API
File:
- `wpcode-snippets/crm/epos-opportunities-api.php`

Purpose:
- registers opportunities REST routes
- supports create, update, list, and detail operations

## WPCode Settings Recommendation
For each snippet:
- snippet type: PHP Snippet
- auto insert: Run Everywhere
- status: Active

## Activation Sequence
1. Create and activate `EPOS Core Bootstrap`
2. Create and activate `EPOS Database Base Tables`
3. Refresh WordPress once so migrations can run on `init`
4. Create and activate `EPOS Clients API`
5. Create and activate `EPOS Leads API`
6. Create and activate `EPOS Opportunities API`

## Initial Validation
After activation, validate these routes while logged in as an administrator:

### Bootstrap Status
- `GET /wp-json/ep/v1/system/bootstrap-status`

Expected result:
- success = true
- namespace = `ep/v1`
- executed_migrations_count >= 1

### Clients
- `GET /wp-json/ep/v1/clients`

### Leads
- `GET /wp-json/ep/v1/leads`

### Opportunities
- `GET /wp-json/ep/v1/opportunities`

## Expected Tables
After migrations run, the following tables should exist with the active WordPress prefix:
- `ep_roles`
- `ep_system_users`
- `ep_clients`
- `ep_leads`
- `ep_opportunities`

## Important Notes
- all current routes are restricted with `manage_options`
- this is temporary for early validation
- frontend integration should not start before these routes respond correctly
- business logic is still incomplete in this phase
