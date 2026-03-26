# Copilot Instructions for Landed Requisition System

## Project Overview
Landed is a school requisition and quotation management system built with PHP and MySQL. It enables staff to create and track requisitions, manage quotations, and provides finance dashboards for approval workflows.

**Stack:**
- Frontend: Bootstrap 5.3.3, PHP with server-side rendering, plain JavaScript
- Backend: PHP (procedural), MySQL 8.0
- Database: `requisition_system` (mysqli connection)
- Server: XAMPP (localhost, no auth token, empty root password)

## Architecture

### Core Components
- **Staff Portal**: Dashboard, requisition/quote creation & management (user-facing)
- **Finance Module**: Finance dashboards for requisition/quote verification and approval
- **Database Layer**: Included in `includes/db.php` (single mysqli connection)

### Page Structure
Each page follows this pattern:
1. **Session management** (`session_start()` at top)
2. **Database include** (`include 'includes/db.php'`)
3. **Data preparation** (fetch from DB or use mock data)
4. **Sidebar inclusion** (`<?php include 'includes/sidebar.php'; ?>`) for layout consistency
5. **HTML template** with Bootstrap and custom CSS

### File Organization
```
landed/
├── [root pages]          # Main views (requisitions.php, quotes.php, dashboard.php, etc)
├── [action pages]        # Save/update handlers (save-requisition.php, update-requisition.php)
├── [management pages]    # Create/edit forms (create_requisition.php, edit-requisition.php)
├── [finance pages]       # Finance dashboard views (finance-*.php)
├── includes/
│   ├── db.php           # Database connection (mysqli)
│   └── sidebar.php      # Navigation component (includes active page detection)
├── assets/
│   ├── css/style.css    # Global styles + component styles
│   ├── js/              # JavaScript (minimal, mostly Bootstrap)
│   └── images/          # Logo and assets
└── uploads/             # Document storage (created on demand)
```

## Key Conventions

### Database Queries
- Use **direct string interpolation** in SQL (not parameterized): `"SELECT * FROM requisitions WHERE id = $id"`
- Connection via `$conn` (global mysqli object from `includes/db.php`)
- **CRITICAL**: SQL injection vulnerability exists—future security improvements should add parameterized queries

### Form Handling
- POST method with `enctype="multipart/form-data"` for file uploads
- File uploads go to `uploads/` directory with timestamp prefix: `time() . "_" . $_FILES['document']['name']`
- Include `required` attribute on HTML inputs (no server-side validation currently)

### Status Values
Valid requisition/quote statuses (used in badges and filtering):
- `'Approved'` → green (`bg-success-subtle`)
- `'Rejected'` → red (`bg-danger-subtle`)
- `'Pending Principal'` → yellow/warning (`bg-warning-subtle`)
- Default status → secondary (`bg-secondary`)

Status display uses `badgeClass()` helper function that returns Bootstrap utility classes.

### Styling & Colors
**Primary Colors** (defined in `assets/css/style.css`):
- **Dark Blue**: `#1F3A5F` (sidebar, primary buttons, "Total" stats)
- **Maroon**: `#7A1F2B` (active nav, role badges, hover states)

**Accent Colors**:
- **Requisitions**: Blue (`#1F3A5F`), Maroon (`#7A1F2B`)
- **Quotes**: Purple (`#6f42c1`), Orange (`#fd7e14`), Teal (`#20c997`)

**Card Pattern**: `.card-box` = white background, 15px padding, 10px border-radius

### Button Styles
- `.btn-primary` → Dark Blue (`#1F3A5F`), hover to Maroon
- `.btn-quote` → Maroon, hover to Dark Blue
- `.btn-edit` → Custom inline style, Dark Blue with Maroon hover
- Action buttons often have inline styles rather than CSS classes

### Sidebar & Navigation
- Sidebar is a shared component (`includes/sidebar.php`)
- Active link detection uses: `$currentPage = basename($_SERVER['PHP_SELF'])`
- Two-column layout: `col-lg-2` sidebar + `col-lg-10` content (10 units)

### Data Flow
1. **Create** → `create_requisition.php` (form) → `save-requisition.php` (INSERT + file upload) → redirect to `requisitions.php`
2. **Edit** → `edit-requisition.php?id=X` (fetch + form) → `update-requisition.php` (UPDATE)
3. **Delete** → `delete-requisition.php?id=X` (DELETE) → redirect

### Session User Data
- User info currently hardcoded in pages: `"Motala Godfrey"`, role: `"admin"` or `"Staff"`
- Session variable available but not consistently used: `$_SESSION['userName']` with fallback

## Common Tasks

### Adding a New Page
1. Create page file (e.g., `new-feature.php`)
2. Start with: `<?php session_start(); include 'includes/db.php'; ?>`
3. Include sidebar: `<?php include 'includes/sidebar.php'; ?>`
4. Wrap content in: `.col-lg-10` inside `.row` inside `.container-fluid`
5. Use Bootstrap grid and `.card-box` components
6. Link in `sidebar.php` navigation

### Creating a Database Query
- Use `$conn->query($sql)` for INSERT/UPDATE/DELETE
- Use `$result->fetch_assoc()` to get single row
- Use loops: `while($row = $result->fetch_assoc())` for multiple rows
- Access connection via global `$conn` from `includes/db.php`

### Formatting Dates
- Use `date("l, d F Y")` for "Monday, 15 March 2026" format
- Use `date("H:i:s")` for time display

### Displaying Tables
Pattern: `<table class="table">` with headers/body, use Bootstrap row utilities for spacing

## Known Issues & Tech Debt
- No input validation or sanitization (XSS/SQL injection risks)
- File uploads lack validation (type/size)
- Session user data hardcoded in pages
- Mixed mock data (arrays) and database queries on same pages
- CSS has duplicates and inline styles scattered across pages
- No error handling for failed database operations
- No front-end build process

## Testing
No automated tests exist. Manual testing required for:
- Form submissions (ensure data saves to DB)
- File uploads (verify files land in `uploads/`)
- Redirects work correctly
- Sidebar active state on all pages
- Status badges display correctly
- Finance dashboard accordions expand/collapse
