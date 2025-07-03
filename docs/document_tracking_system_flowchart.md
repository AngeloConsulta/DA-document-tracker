# Document Tracking System - Complete Flowchart Diagram

## System Overview Flowchart

```mermaid
flowchart TD
    A[START] --> B[User Login]
    B --> C{Authentication Success?}
    C -->|No| D[Login Failed]
    C -->|Yes| E[Role Verification]
    
    E --> F{User Role?}
    
    %% Superadmin Flow
    F -->|Superadmin| G[All Access Granted]
    G --> G1[Dashboard Access]
    G --> G2[Scanner Access]
    G --> G3[All Documents Access]
    G --> G4[Incoming Documents Access]
    G --> G5[Outgoing Documents Access]
    G --> G6[Department Management Access]
    G --> G7[User Management Access]
    G --> G8[System Configuration Access]
    
    %% Admin Flow
    F -->|Admin| H[Limited Access Granted]
    H --> H1[Dashboard Access]
    H --> H2[Scanner Access]
    H --> H3[All Documents Access - Department Filtered]
    H --> H4[Incoming Documents Access - Department Filtered]
    H --> H5[Outgoing Documents Access - Department Filtered]
    H --> H6[Department Access - Own Department Only]
    
    %% Department User Flow
    F -->|Department User| I[Restricted Access Granted]
    I --> I1[Dashboard Access - Own Department]
    I --> I2[Scanner Access]
    I --> I3[All Documents Access - Own Department]
    I --> I4[Incoming Documents Access - Own Department]
    I --> I5[Outgoing Documents Access - Own Department]
    I --> I6[Document Creation & Routing Access]
    
    %% Continue to main system flows
    G1 --> J[Main System Interface]
    H1 --> J
    I1 --> J
    
    D --> K[END]
```

## Document Creation & Routing Flow (Department User Focus)

```mermaid
flowchart TD
    A[Department User Login] --> B[Dashboard]
    B --> C[Outgoing Documents Page]
    C --> D[Create New Document]
    
    D --> E[Document Details Entry]
    E --> E1[Title & Description]
    E --> E2[Document Type Selection]
    E --> E3[Department Assignment - Auto-assigned]
    E --> E4[File Upload]
    E --> E5[Priority & Due Date]
    
    E1 --> F[QR Code Generation]
    E2 --> F
    E3 --> F
    E4 --> F
    E5 --> F
    
    F --> G[Document History Log]
    G --> H[Status Assignment]
    H --> I[Document Created Successfully]
    I --> J[Display in All Documents Page]
    
    %% Document Forwarding Process
    J --> K[Select Document for Forwarding]
    K --> L[Forward Action]
    
    L --> M[Forward to Department Selection]
    M --> M1[Choose Target Department]
    M --> M2[Assign to Specific User - Optional]
    M --> M3[Add Remarks/Notes]
    M --> M4[Set Priority Level]
    
    M1 --> N[Document Status Update - Forwarded]
    M2 --> N
    M3 --> N
    M4 --> N
    
    N --> O[Document History Log - Routing Entry]
    O --> P[Notification Creation]
    P --> Q[Target Department User Alert]
    Q --> R[Document Appears in Target User's Incoming Documents Page]
```

## Document Flow Between Departments

```mermaid
flowchart LR
    subgraph "Department A"
        A1[Department A User]
        A2[Outgoing Documents Page]
        A3[Document Creation]
    end
    
    subgraph "System Processing"
        B1[Document Status: Outgoing]
        B2[Document History: Routing Entry]
        B3[Notification System]
    end
    
    subgraph "Department B"
        C1[Department B User]
        C2[Incoming Documents Page]
        C3[Document Received]
    end
    
    A1 --> A2
    A2 --> A3
    A3 --> B1
    B1 --> B2
    B2 --> B3
    B3 --> C1
    C1 --> C2
    C2 --> C3
    
    B1 -.->|Status Change| B1
    B1 -.->|Forwarded| C3
    C3 -.->|Status Change| C3
    C3 -.->|Received| C3
```

## Role-Based Navigation Flow

```mermaid
flowchart TD
    A[User Login] --> B{User Role?}
    
    %% Superadmin Navigation
    B -->|Superadmin| C[Superadmin Dashboard]
    C --> C1[System Overview & Statistics]
    C --> C2[QR Scanner]
    C --> C3[Documents Dropdown]
    C --> C4[User Management]
    C --> C5[Departments]
    C --> C6[Document Types]
    C --> C7[Document Statuses]
    
    C3 --> C31[All Documents - Complete List]
    C3 --> C32[Incoming Documents - All]
    C3 --> C33[Outgoing Documents - All]
    
    %% Admin Navigation
    B -->|Admin| D[Admin Dashboard]
    D --> D1[Department Overview & Statistics]
    D --> D2[QR Scanner]
    D --> D3[Documents Dropdown]
    D --> D4[Departments - Own Department Only]
    
    D3 --> D31[All Documents - Department Filtered]
    D3 --> D32[Incoming Documents - Department Filtered]
    D3 --> D33[Outgoing Documents - Department Filtered]
    
    %% Department User Navigation
    B -->|Department User| E[Department User Dashboard]
    E --> E1[Department Overview & Statistics]
    E --> E2[QR Scanner]
    E --> E3[Documents Dropdown]
    E --> E4[Profile]
    
    E3 --> E31[All Documents - Own Department]
    E3 --> E32[Incoming Documents - Own Department]
    E3 --> E33[Outgoing Documents - Own Department]
```

## Document Status Tracking Flow

```mermaid
flowchart LR
    A[Document Created] --> B[Draft Status]
    B --> C[Ready Status]
    C --> D[Forwarded Status]
    D --> E[Received Status]
    E --> F[Processing Status]
    F --> G[Under Review Status]
    G --> H[Approved Status]
    H --> I[Completed Status]
    I --> J[Archived Status]
    
    %% Status change triggers
    B -.->|User Action| C
    C -.->|Forward Action| D
    D -.->|Department Receives| E
    E -.->|User Processes| F
    F -.->|Review Required| G
    G -.->|Approval Decision| H
    H -.->|Work Complete| I
    I -.->|System Action| J
```

## Notification & Alert System Flow

```mermaid
flowchart TD
    A[Document Forwarded] --> B[Notification System Trigger]
    B --> C[Target User Notification Creation]
    
    C --> C1[Notification Type: Document Forwarded]
    C --> C2[Message: Document Tracking Number forwarded]
    C --> C3[From Department: Source Department]
    C --> C4[To Department: Target Department]
    C --> C5[Document Link: Direct Access]
    
    C1 --> D[Real-time Notification Delivery]
    C2 --> D
    C3 --> D
    C4 --> D
    C5 --> D
    
    D --> E[Email Notification - Optional]
    D --> F[Notification Display]
    F --> G[Target User's Incoming Documents Page]
```

## QR Code Integration Flow

```mermaid
flowchart TD
    A[Document Created] --> B[QR Code Generation]
    B --> C[QR Code Storage]
    C --> D[QR Code Display]
    D --> E[Document Details]
    
    C --> F[QR Code Scanning]
    F --> G[Document Lookup]
    G --> H[Access Verification]
    H --> I[Status Update]
    I --> J[QR Scanner Interface]
    J --> K[History Log]
    K --> L[QR Code Usage Tracking]
```

## Security & Access Control Flow

```mermaid
flowchart TD
    A[User Request] --> B[Authentication]
    B --> C[Authorization]
    C --> D[Role Check]
    D --> E[Department Filter]
    E --> F[Permission Verification]
    F --> G{Access Granted?}
    
    G -->|Yes| H[Action Execution]
    G -->|No| I[Access Denied - 403 Error]
    
    H --> J[Audit Log]
    J --> K[Success Response]
    
    I --> L[Error Response]
    
    K --> M[END]
    L --> M
```

## Document Lifecycle Management Flow

```mermaid
flowchart TD
    subgraph "Incoming Documents Processing"
        A1[Document Received] --> A2[Incoming Documents Page]
        A2 --> A3[User Action]
        A3 --> A4[Document Review]
        A4 --> A5[Status Update]
        A5 --> A6[Document Processing]
        A6 --> A7[Internal Routing]
        A7 --> A8[Document Approval]
        A8 --> A9[Status Change]
        A9 --> A10[Document Completion]
        A10 --> A11[Archive/Forward]
    end
    
    subgraph "Outgoing Documents Management"
        B1[Document Creation] --> B2[Outgoing Documents Page]
        B2 --> B3[Document Management]
        B3 --> B4[Document Draft]
        B4 --> B5[Save for Later]
        B5 --> B6[Document Ready]
        B6 --> B7[Forward to Department]
        B7 --> B8[Document Sent]
        B8 --> B9[Status Update]
        B9 --> B10[Document Tracking]
        B10 --> B11[History Log]
    end
```

## Error Handling & Recovery Flow

```mermaid
flowchart TD
    A[System Operation] --> B{Error Detected?}
    B -->|No| C[Continue Normal Operation]
    B -->|Yes| D[Error Logging]
    
    D --> E[Error Classification]
    E --> F{Error Type?}
    
    F -->|Authentication Error| G[Redirect to Login]
    F -->|Authorization Error| H[Show 403 Error Page]
    F -->|Validation Error| I[Show Validation Messages]
    F -->|System Error| J[Show Generic Error Page]
    
    G --> K[User Notification]
    H --> K
    I --> K
    J --> K
    
    K --> L[Recovery Attempt]
    L --> M{Recovery Successful?}
    M -->|Yes| N[Continue Operation]
    M -->|No| O[Fallback Options]
    
    O --> P[Show Error Details]
    P --> Q[Log Error for Admin]
    Q --> R[END]
    
    C --> S[END]
    N --> S
```

## Complete System Integration Flow

```mermaid
flowchart TD
    A[User Access Request] --> B[Authentication Layer]
    B --> C[Authorization Layer]
    C --> D[Role-Based Access Control]
    D --> E[Department Filtering]
    E --> F[Permission Verification]
    
    F --> G{Access Granted?}
    G -->|Yes| H[Database Interaction]
    G -->|No| I[Access Denied Response]
    
    H --> J[Document Operations]
    J --> K[File System Operations]
    K --> L[QR Code Generation/Scanning]
    L --> M[Notification System]
    M --> N[Real-time Broadcasting]
    N --> O[Audit Logging]
    O --> P[Response Generation]
    
    I --> Q[Error Response]
    
    P --> R[END]
    Q --> R
```

## Color Coding Legend

- **Purple**: Superadmin operations and access
- **Blue**: Admin operations and access  
- **Green**: Department User operations and access
- **Orange**: System processes and notifications
- **Red**: Error handling and security
- **Gray**: Database and file operations

## Key System Features Highlighted

1. **Role-Based Access Control (RBAC)** with Department Filtering
2. **Document Lifecycle Management** with Incoming/Outgoing Flow
3. **QR Code Integration** for Document Tracking
4. **Real-time Notifications** for Document Forwarding
5. **Audit Trail & History** with Routing Information
6. **Export & Reporting** with Role-Based Filtering
7. **Mobile-Responsive Design** for QR Scanner
8. **Multi-department Support** with Inter-department Routing
9. **File Upload & Management** with Department Restrictions
10. **Advanced Search & Filtering** by Department and Status

This comprehensive flowchart diagram provides a complete visual representation of the Document Tracking System, showing all the key flows, role-based access control, document creation and forwarding processes, and system integrations as specified in the updated FLOWCHART_PROMPT.md. 