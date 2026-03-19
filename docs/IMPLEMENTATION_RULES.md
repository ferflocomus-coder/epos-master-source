# IMPLEMENTATION_RULES

## 1. Runtime
Evolution Power OS (EPOS) runs entirely inside WordPress.

## 2. Backend
Use:
- PHP
- WordPress REST API
- Custom database tables via $wpdb

Do NOT use:
- Node.js
- Express
- external backend servers

## 3. Implementation Method
All modules must be implemented as independent WPCode snippets.

No WordPress plugins should be created.

## 4. WordPress Admin Access
WordPress admin (/wp-admin) is accessible only to the system developer.

Operational users never access WordPress.

## 5. Application Interface
The application interface is the dashboard defined in:

frontend/ui/dashboard.html

Operational users interact only with this dashboard.

## 6. Architecture Model
WordPress acts only as:
- runtime
- database layer
- REST API provider

The operational system runs through the custom dashboard UI.
