# Clinic 1 - Enterprise Medical Management System (Clinic _App)

## 1. Overview
A secure, production-ready clinic and medical management system built with Laravel 11 and modern UI standards, adhering to strict business logic, enterprise security architecture, and anonymized naming standards.

---

## 2. Naming Standards (Strict Zero Personal Names Policy)
- **Clinic Entity**: `Clinic 1`
- **Users**:
  - `name 1` (Admin)
  - `name 2` (Owner)
  - `name 3` (Doctor)
  - `name 4` (Receptionist)
  - `name 5` (Patient)
- **Locations**: `Location 1`, `Location 2`

---

## 3. High-Security & Zero-Bug Architecture
1. **Server-Side Role Identification (No Role Input in Login)**:
   - Login endpoint (`/api/auth/login`) accepts only credentials (`username` / `email` / `phone` + `password`).
   - The user's role is extracted exclusively from the trusted database record.
   - Any client-submitted `role` field is stripped and ignored (`LoginRequest`).

2. **Strict RBAC & Middleware Enforcement**:
   - `EnsureUserHasRole` middleware verifies token abilities (`role:admin`, `role:receptionist`, `role:doctor`).
   - Direct controller authorization checks prevent cross-role privilege escalation.

3. **Invoicing & Billing Protection (Read-Only Lock)**:
   - **Pre-Payment Discounts**: Receptionist can apply discounts and edit line items only before payment.
   - **Read-Only Lock**: Once the invoice is marked as `paid` (full payment received), `is_locked` is set to `true`, and editing line items or discounts is rejected with `422 Unprocessable Entity`.
   - **Refund Capability**: Only receptionists can execute refunds on paid invoices (`/api/invoices/{id}/refund`), with mandatory audit logging and reason tracking.

4. **Appointment Lifecycle Governance**:
   - Strict transition steps: `Pending` ➡️ `Confirmed` ➡️ `Completed` ➡️ `Cancelled`.
   - Urgent booking flag (`is_urgent`) with visual alerts for doctor attention.

---

## 4. Test Suite Verification
All 22 unit and feature tests pass with 0 errors and 0 bugs:
```bash
php artisan test
```
Test categories covered:
- Authentication & Role Auto-Resolution (`AuthApiTest`)
- Role-based Authorization Matrix (`RoleAuthorizationTest`)
- Appointment Lifecycle & Urgent Bookings (`AppointmentLifecycleTest`)
- Invoice Line Items, Read-Only Lock & Receptionist Refunds (`InvoiceLifecycleAndRefundTest`)

---

## 5. Directory Locations on Machine
- **Standalone Folder**: `D:\laravel\Clinic _App\`
- **Project Folder**: `D:\laravel\my-new-project\Clinic _App\`
