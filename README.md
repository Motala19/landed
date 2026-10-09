# Midrand Primary School — Requisition Management System

A web-based requisition and approval system built for **Midrand Primary School**.  
It digitises the full flow from request creation to final payment, with role-based access, document uploads, budgets, and audit tracking.

**Live environment:** School-hosted on xneelo (internal use)

---

## Overview

Staff submit purchase/payment requisitions online. Requests move through a clear approval chain:

1. **Staff** — create and track own requisitions  
2. **Finance** — verify (budget check) and process payments  
3. **Principal** — review and approve/reject  
4. **Treasurer** — final approval  
5. **Finance** — mark as Paid and attach Proof of Payment (POP)  
6. **Staff** — may upload invoice after payment  

The system reduces paper tracking, improves accountability, and supports school governance and audit needs.

---

## Features

### Core workflow
- Role-based dashboards (Staff, Finance, Principal, Treasurer, Admin)
- Multi-stage status flow: New → Pending → Principal Approved → Approved → Paid / Rejected
- Soft delete (hide from a role’s view without destroying records)
- Supporting document upload (with optional late upload if forgotten at submit)
- Proof of Payment (POP) and invoice handling
- Rejection reasons with “Rejected by” accountability

### Security
- Secure login (email + password)
- Password hashing (`password_hash` / `password_verify`)
- Forced password change on first login (temporary passwords)
- Login attempt limiting / temporary lockout
- Session timeout
- Optional email OTP (MFA) — can be enabled when mail delivery is reliable
- Prepared statements for database queries
- Role-restricted pages

### Administration & reporting
- User management (Finance / Admin)
- Audit / process log of key actions
- Reports & department budgets
- Budget allocation (Finance)
- Year-end archive and reset (Admin)
- PDF-style year reports (print / save as PDF)

### Notifications
- Email alerts at key stages (new request, approvals, etc.)
- Depends on school mail configuration

---

## Tech stack

| Layer | Technology |
|--------|------------|
| Frontend | HTML, CSS, Bootstrap 5 |
| Backend | PHP |
| Database | MySQL |
| Hosting | xneelo (shared hosting) |
| Other | Session auth, file uploads, PHP `mail()` |

---

## User roles

| Role | Main responsibilities |
|------|------------------------|
| **Staff** | Create/view own requisitions, upload invoice after payment |
| **Finance** | Verify requests, manage users, mark paid, POP, budgets |
| **Principal** | Approve/reject after Finance verification |
| **Treasurer** | Final approval/rejection |
| **Admin** | System overview, user management, year-end budget reset |

---

## Project structure (typical)

```text
/
├── login.php
├── logout.php
├── change-password.php
├── requisitions.php
├── create_requisition.php
├── save-requisition.php
├── finance-dashboard.php
├── principal-dashboard.php
├── treasurer-dashboard.php
├── dashboard.php              # Admin
├── manage-users.php
├── reports.php
├── budget-allocate.php
├── reports-processes.php
├── view-requisition.php
├── includes/
│   ├── db.php
│   ├── sidebar.php
│   ├── notification.php
│   └── audit_logger.php
├── assets/
│   ├── css/style.css
│   └── images/
└── uploads/                   # Documents, POP, invoices (not in git)

## Author

**Motala Godfrey Mogale**  
Built for real school use — requisitions, multi-stage approvals, payments, and department budgets.

### Contact

- **Email:** [tshepisogodfrey@gmail.com](mailto:tshepisogodfrey@gmail.com)  
- **Phone:** 065 984 7909  

Open to **junior** or **mid-level** developer opportunities (PHP, MySQL, web applications).
