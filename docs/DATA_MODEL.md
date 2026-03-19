# DATA_MODEL

## Tables

### ep_clients
Primary key:
- id

Fields:
- id
- full_name
- email
- phone
- address_line_1
- address_line_2
- city
- state
- postal_code
- source
- status
- created_at
- updated_at

Relationships:
- one client can have many leads
- one client can have many opportunities
- one client can have many projects
- one client can have many documents

### ep_leads
Primary key:
- id

Fields:
- id
- client_id
- lead_source
- campaign_name
- assigned_user_id
- status
- notes
- created_at
- updated_at

Relationships:
- belongs to ep_clients via client_id
- can convert into an opportunity

### ep_opportunities
Primary key:
- id

Fields:
- id
- client_id
- lead_id
- business_id
- title
- description
- pipeline_stage
- status
- estimated_value
- assigned_user_id
- created_at
- updated_at

Relationships:
- belongs to ep_clients via client_id
- may originate from ep_leads via lead_id
- can have many estimators
- can have many contracts
- can create one or more projects

### ep_estimators
Primary key:
- id

Fields:
- id
- opportunity_id
- project_id
- address
- roof_type
- material_type
- roof_area
- slope
- estimated_material_cost
- estimated_labor_cost
- total_estimated_price
- sales_rep_id
- status
- created_at
- updated_at

Relationships:
- belongs to ep_opportunities via opportunity_id
- may belong to ep_projects via project_id
- has many estimator items
- may generate documents and contracts

### ep_estimator_items
Primary key:
- id

Fields:
- id
- estimator_id
- item_type
- item_name
- unit
- quantity
- unit_cost
- total_cost
- sort_order
- created_at
- updated_at

Relationships:
- belongs to ep_estimators via estimator_id

### ep_contracts
Primary key:
- id

Fields:
- id
- opportunity_id
- estimator_id
- project_id
- contract_number
- client_name
- client_email
- contract_amount
- status
- contract_url
- signed_at
- created_at
- updated_at

Relationships:
- belongs to ep_opportunities via opportunity_id
- may belong to ep_estimators via estimator_id
- may create or link to ep_projects via project_id
- can have document signatures

### ep_projects
Primary key:
- id

Fields:
- id
- client_id
- opportunity_id
- contract_id
- business_id
- title
- description
- address
- status
- start_date
- end_date
- created_at
- updated_at

Relationships:
- belongs to ep_clients via client_id
- may originate from ep_opportunities via opportunity_id
- may originate from ep_contracts via contract_id
- has many project tasks
- may have many installers through assignments
- has many documents

### ep_project_tasks
Primary key:
- id

Fields:
- id
- project_id
- stage_id
- assigned_user_id
- title
- description
- priority
- status
- due_date
- created_at
- updated_at

Relationships:
- belongs to ep_projects via project_id
- belongs to stage structure by stage_id
- may belong to a system user via assigned_user_id

### ep_installers
Primary key:
- id

Fields:
- id
- full_name
- company_name
- email
- phone
- trade_type
- status
- notes
- created_at
- updated_at

Relationships:
- can be assigned to many projects
- can be linked to commissions

### ep_commissions
Primary key:
- id

Fields:
- id
- project_id
- contract_id
- installer_id
- user_id
- commission_type
- base_amount
- commission_amount
- status
- approved_at
- paid_at
- created_at
- updated_at

Relationships:
- belongs to ep_projects via project_id
- may belong to ep_contracts via contract_id
- may belong to ep_installers via installer_id
- may belong to system users via user_id

### ep_documents
Primary key:
- id

Fields:
- id
- entity_type
- entity_id
- document_type
- title
- file_url
- status
- created_by
- created_at
- updated_at

Relationships:
- polymorphic link to clients, opportunities, estimators, contracts, or projects via entity_type and entity_id
- can have many signatures

### ep_document_signatures
Primary key:
- id

Fields:
- id
- document_id
- signer_name
- signer_email
- signer_role
- signed_at
- signature_url
- status
- created_at
- updated_at

Relationships:
- belongs to ep_documents via document_id

### ep_notifications
Primary key:
- id

Fields:
- id
- user_id
- type
- title
- message
- entity_type
- entity_id
- is_read
- created_at
- updated_at

Relationships:
- belongs to system users via user_id
- may reference any operational entity via entity_type and entity_id

### ep_system_users
Primary key:
- id

Fields:
- id
- wp_user_id
- role_id
- full_name
- email
- phone
- status
- created_at
- updated_at

Relationships:
- maps to WordPress users via wp_user_id
- belongs to roles via role_id
- can own leads, opportunities, tasks, notifications, and commissions

### ep_roles
Primary key:
- id

Fields:
- id
- name
- slug
- description
- status
- created_at
- updated_at

Relationships:
- one role can belong to many system users
