# API_SPEC

Base namespace:
/wp-json/ep/v1/

## clients

### GET /clients
Parameters:
- page
- per_page
- search
- status

Response:
- success
- data[]
- pagination

### POST /clients
Parameters:
- full_name
- email
- phone
- address_line_1
- city
- state
- postal_code
- source
- status

Response:
- success
- message
- data

### GET /clients/{id}
Response:
- success
- data

### PUT /clients/{id}
Parameters:
- full_name
- email
- phone
- address fields
- status

Response:
- success
- message
- data

## leads

### GET /leads
Parameters:
- page
- per_page
- assigned_user_id
- status
- search

### POST /leads
Parameters:
- client_id
- lead_source
- campaign_name
- assigned_user_id
- status
- notes

### GET /leads/{id}
### PUT /leads/{id}

## opportunities

### GET /opportunities
Parameters:
- page
- per_page
- client_id
- assigned_user_id
- pipeline_stage
- status

### POST /opportunities
Parameters:
- client_id
- lead_id
- business_id
- title
- description
- pipeline_stage
- status
- estimated_value
- assigned_user_id

### GET /opportunities/{id}
### PUT /opportunities/{id}

## estimators

### GET /estimators
Parameters:
- page
- per_page
- opportunity_id
- project_id
- sales_rep_id
- status

### POST /estimators
Parameters:
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

### GET /estimators/{id}
### PUT /estimators/{id}

## estimator-items

### GET /estimator-items
Parameters:
- estimator_id

### POST /estimator-items
Parameters:
- estimator_id
- item_type
- item_name
- unit
- quantity
- unit_cost
- total_cost
- sort_order

### GET /estimator-items/{id}
### PUT /estimator-items/{id}

## contracts

### GET /contracts
Parameters:
- page
- per_page
- opportunity_id
- project_id
- status

### POST /contracts
Parameters:
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

### GET /contracts/{id}
### PUT /contracts/{id}

## projects

### GET /projects
Parameters:
- page
- per_page
- client_id
- business_id
- status
- search

### POST /projects
Parameters:
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

### GET /projects/{id}
### PUT /projects/{id}

## project-tasks

### GET /project-tasks
Parameters:
- page
- per_page
- project_id
- stage_id
- assigned_user_id
- status

### POST /project-tasks
Parameters:
- project_id
- stage_id
- assigned_user_id
- title
- description
- priority
- status
- due_date

### GET /project-tasks/{id}
### PUT /project-tasks/{id}

## installers

### GET /installers
Parameters:
- page
- per_page
- trade_type
- status
- search

### POST /installers
Parameters:
- full_name
- company_name
- email
- phone
- trade_type
- status
- notes

### GET /installers/{id}
### PUT /installers/{id}

## commissions

### GET /commissions
Parameters:
- page
- per_page
- project_id
- contract_id
- installer_id
- user_id
- status

### POST /commissions
Parameters:
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

### GET /commissions/{id}
### PUT /commissions/{id}

## documents

### GET /documents
Parameters:
- page
- per_page
- entity_type
- entity_id
- document_type
- status

### POST /documents
Parameters:
- entity_type
- entity_id
- document_type
- title
- file_url
- status
- created_by

### GET /documents/{id}
### PUT /documents/{id}

## notifications

### GET /notifications
Parameters:
- page
- per_page
- user_id
- is_read
- type

### POST /notifications
Parameters:
- user_id
- type
- title
- message
- entity_type
- entity_id
- is_read

### GET /notifications/{id}
### PUT /notifications/{id}

## system-users

### GET /system-users
Parameters:
- page
- per_page
- role_id
- status
- search

### POST /system-users
Parameters:
- wp_user_id
- role_id
- full_name
- email
- phone
- status

### GET /system-users/{id}
### PUT /system-users/{id}

## roles

### GET /roles
Parameters:
- page
- per_page
- status

### POST /roles
Parameters:
- name
- slug
- description
- status

### GET /roles/{id}
### PUT /roles/{id}

## Standard Response Shape
Success response:
- success: true
- message: string
- data: object or array

Error response:
- success: false
- message: string
- errors: array
