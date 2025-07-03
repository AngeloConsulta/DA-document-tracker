# DARFO-5 Document Tracker

## Overview
The DARFO-5 Document Tracker is a comprehensive, secure, and scalable document tracking system developed for the Department of Agriculture Regional Field Office 5 (DARFO-5). Built on Laravel, it streamlines the management, routing, and monitoring of official documents across multiple departments, ensuring transparency, accountability, and real-time status updates throughout the document lifecycle.

---

## Key Features
- **Role-Based Access Control (RBAC):** Hierarchical permissions for Superadmin, Admin, and Department Users, with department-based filtering and granular access to documents and system functions.
- **Document Lifecycle Management:** Full support for document creation, editing, forwarding, status tracking, and archiving, with detailed history and audit trails.
- **QR Code Integration:** Automatic QR code generation for each document, enabling fast lookup, tracking, and status updates via a mobile-responsive QR scanner interface.
- **Real-Time Notifications:** Users receive instant alerts when documents are forwarded, received, or updated, ensuring timely action and accountability.
- **Multi-Department Support:** Documents can be routed between division/offices with access and actions restricted based on user roles and department assignments.
- **Advanced Search & Filtering:** Powerful search and filter options by department, status, type, and more.
- **Export & Reporting:** Role-based export of document histories and statistics for compliance and analytics.
- **Mobile-Responsive Design:** Optimized for use on both desktop and mobile devices, including the QR scanner.
- **Audit Trail:** Every action is logged for security and compliance.

---

## System Architecture
- **Backend:** Laravel PHP Framework
- **Frontend:** Blade Templates, Tailwind CSS, JavaScript
- **Database:** MySQL
- **Authentication:** Laravel Auth with RBAC
- **QR Code:** Generation and scanning using integrated libraries
- **Notifications:** Real-time via Laravel broadcasting and in-app alerts

---

## User Roles & Permissions
- **Superadmin:**
  - Full access to all documents, departments, users, and system settings
  - Can manage users and departments across the organization
- **Admin:**
  - Access limited to their assigned department
  - Can manage users and documents within their department
- **Department User:**
  - Can create, view, and edit documents within their department
  - Can forward documents to other departments
  - No user management permissions

---

## Core Flows
### 1. Authentication & Role-Based Navigation
- Users log in and are directed to dashboards and features based on their role.
- Superadmins see all data; Admins and Department Users see only their department's data.

### 2. Document Creation & Routing
- Department Users create documents, which are auto-assigned to their department.
- Documents can be forwarded to other departments or users, with status and history updated at each step.
- QR codes are generated for each document for easy tracking.

### 3. QR Code Scanning
- Users can scan document QR codes using the built-in scanner (mobile/desktop supported).
- The scanner finds documents by QR code value or image path, and allows status updates if permitted.

### 4. Notifications & Alerts
- Real-time notifications are sent when documents are forwarded, received, or updated.
- Alerts include document details, source/target departments, and direct links.

### 5. Document Status Tracking
- Documents move through statuses: Draft → Ready → Forwarded → Received → Processing → Under Review → Approved → Completed → Archived.
- All status changes are logged and visible in the document history.

### 6. Security & Compliance
- All access is protected by authentication, authorization, and department-based filtering.
- Policies, middleware, and UI controls ensure users only see and act on permitted data.
- Audit logs and error handling provide traceability and reliability.

---

## Technical Highlights
- **Middleware:** Enforces document access rules on all relevant routes.
- **Policies & Services:** Centralized logic for permissions and department filtering.
- **Comprehensive Testing:** Feature tests for access control, QR scanning, and document flows.
- **Extensible:** Easily add new roles, departments, or document types as needed.

---

## Getting Started
1. **Clone the repository** and install dependencies:
   ```bash
   git clone <repo-url>
   cd document-tracker
   composer install
   npm install && npm run dev
   cp .env.example .env
   php artisan key:generate
   ```
2. **Configure your database** in `.env` and run migrations:
   ```bash
   php artisan migrate --seed
   ```
3. **Start the development server:**
   ```bash
   php artisan serve
   ```
4. **Access the app** at `http://localhost:8000` and log in with seeded users.

---

## Documentation
- [System Flowcharts](docs/document_tracking_system_flowchart.md)
- [Role-Based Access Control](docs/ROLE_BASED_ACCESS_CONTROL.md)
- [QR Scanner Features](docs/README_QR_SCANNER.md)

---

## License
This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
