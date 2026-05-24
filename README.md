# HRMS — Human Resource Management System

A full-featured Human Resource Management System built with Laravel 11, Blade, TailwindCSS, and MySQL. Designed for real-world internal use with role-based access control, payroll generation, attendance tracking, leave management, and more.

---

## Table of contents

- [Features](#features)
- [Tech stack](#tech-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Default credentials](#default-credentials)
- [Roles and permissions](#roles-and-permissions)
- [Module overview](#module-overview)
- [Project structure](#project-structure)
- [API endpoints](#api-endpoints)
- [Configuration](#configuration)
- [Screenshots](#screenshots)
- [Future improvements](#future-improvements)
- [License](#license)

---

## Features

- **Employee management** — full employee profiles with department and position assignment, profile photo upload, and soft-delete termination flow with restore and permanent delete options
- **Attendance tracking** — daily check-in and check-out with automatic late detection, monthly attendance reports with present, late, and absent summaries
- **Leave management** — configurable leave types, balance tracking, approval workflow with HR notifications, and leave history per employee
- **Payroll system** — automated salary calculation with allowances, deductions, and absence adjustments; one-click PDF payslip generation; bulk payroll generation for all active employees
- **Performance reviews** — scored evaluations by period with strengths, improvement areas, and comments; full review history per employee
- **Role-based access control** — three roles (Admin, HR Manager, Employee) with middleware and policy-based authorization
- **Analytics dashboard** — real-time stats cards and Chart.js charts for attendance trends, department distribution, and monthly payroll costs
- **Reports and exports** — employee, payroll, and attendance reports exportable as PDF (DomPDF) or Excel (Maatwebsite)
- **REST API** — Sanctum-authenticated JSON API for all core resources
- **Notifications** — database notifications for leave submissions, approvals, rejections, and performance reviews
- **Audit logging** — every create and update action on employee records is logged with old and new values and the acting user's IP address

---

## Tech stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11 |
| Frontend | Blade + TailwindCSS |
| Database | MySQL 8+ |
| Authentication | Laravel Breeze |
| Authorization | Policies + Role middleware |
| API auth | Laravel Sanctum |
| PDF generation | barryvdh/laravel-dompdf |
| Excel export | Maatwebsite/Laravel-Excel |
| Charts | Chart.js |
| PHP | 8.2+ |

---

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8+
- Apache or Nginx (or `php artisan serve` for local development)

PHP extensions required: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo`, `gd`

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-username/laravel-hr-management-system.git
cd laravel-hr-management-system
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node dependencies and build assets

```bash
npm install
npm run build
```

### 4. Copy the environment file

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure your environment

Open `.env` and update the following values:

```env
APP_NAME="HR Management System"
APP_URL=http://localhost/laravel-hr-management-system/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_hrms
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 6. Create the database

```sql
CREATE DATABASE laravel_hrms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 7. Run migrations

```bash
php artisan migrate
```

### 8. Seed sample data

```bash
php artisan db:seed
```

### 9. Create the storage symlink

```bash
php artisan storage:link
```

### 10. Start the development server

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

---

## Default credentials

| Role | Email | Password |
|---|---|---|
| Admin | admin@hrms.com | password |
| HR Manager | hr@hrms.com | password |
| Employee | emp1@hrms.com | password |
| Employee | emp2@hrms.com | password |

> **Note:** Change all passwords immediately before deploying to production.

---

## Roles and permissions

### Admin
- Full system access
- Manage departments, positions, and all employees
- Generate, approve, and mark payroll as paid
- Permanently delete terminated employees
- View all reports and audit logs
- Access REST API

### HR Manager
- Manage employee profiles (create, update, terminate)
- View and filter attendance records
- Approve or reject leave requests
- Submit performance reviews
- Export reports as PDF or Excel

### Employee
- View own profile
- Daily check-in and check-out
- Submit leave requests and view balance
- Download own PDF payslips
- View own attendance history
- Receive in-app notifications

---

## Module overview

### Dashboard
Real-time metrics showing total active employees, present today, on leave today, and pending leave requests. Three Chart.js charts: monthly attendance bar chart, department distribution doughnut, and 6-month payroll cost line chart.

### Employee management
Full CRUD with profile photo upload, department and position assignment, hire date, salary, gender, and contact details. Soft-delete termination preserves all historical records. Admin can restore or permanently delete terminated employees.

### Department management
Create and manage departments with optional manager assignment. Department detail page shows all assigned employees.

### Position management
Create positions with level classification (junior, mid, senior, lead, manager, executive). Position detail page shows all employees in that role.

### Attendance tracking
Employees check in and check out via the web interface. The system detects late arrivals (after 09:00). HR managers view all records filterable by employee and date. Monthly report shows present, late, and absent counts per employee.

### Leave management
Configurable leave types (annual, sick, unpaid, maternity) with allowed days per year. Employees submit requests with date range and reason. The system validates the remaining balance before submission. HR managers approve or reject with an optional rejection reason. Both parties receive database notifications on status change.

### Payroll system
Admin generates payroll per employee or for all active employees for a given month. The system calculates basic salary, housing allowance (20%), transport allowance (10%), income tax (10%), social security (5%), and absence deductions based on actual attendance. PDF payslips are generated on demand with DomPDF.

### Performance reviews
HR managers submit scored reviews (1–100) per employee per period (e.g. Q1-2024) with strengths, improvement areas, and comments. Employees receive a notification when a review is submitted.

### Reports
Three report types: employee list, payroll summary by month, and attendance records by month. Each report can be viewed in the browser, exported as PDF, or downloaded as an Excel file.

### Notifications
All in-app notifications use Laravel's database channel. HR managers are notified when an employee submits a leave request. Employees are notified when their leave is approved or rejected, and when a performance review is submitted for them.

---

## Project structure

```
app/
├── Exports/
│   ├── EmployeesExport.php
│   └── PayrollExport.php
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── AttendanceController.php
│   │   │   ├── EmployeeController.php
│   │   │   ├── LeaveRequestController.php
│   │   │   └── PayrollController.php
│   │   ├── AttendanceController.php
│   │   ├── DashboardController.php
│   │   ├── DepartmentController.php
│   │   ├── EmployeeController.php
│   │   ├── LeaveRequestController.php
│   │   ├── NotificationController.php
│   │   ├── PayrollController.php
│   │   ├── PerformanceReviewController.php
│   │   ├── PositionController.php
│   │   └── ReportController.php
│   ├── Middleware/
│   │   └── RoleMiddleware.php
│   ├── Requests/
│   │   ├── StoreEmployeeRequest.php
│   │   ├── StoreLeaveRequest.php
│   │   ├── StorePerformanceReviewRequest.php
│   │   └── UpdateEmployeeRequest.php
│   └── Resources/
│       └── EmployeeResource.php
├── Models/
│   ├── Attendance.php
│   ├── AuditLog.php
│   ├── Department.php
│   ├── Employee.php
│   ├── LeaveRequest.php
│   ├── LeaveType.php
│   ├── Payroll.php
│   ├── PayrollItem.php
│   ├── PerformanceReview.php
│   ├── Position.php
│   ├── Role.php
│   └── User.php
├── Notifications/
│   ├── LeaveRequestNotification.php
│   ├── LeaveStatusNotification.php
│   └── PerformanceReviewNotification.php
├── Policies/
│   └── EmployeePolicy.php
├── Providers/
│   └── AppServiceProvider.php
└── Services/
    ├── EmployeeService.php
    └── PayrollService.php

database/
├── migrations/
└── seeders/
    ├── AdminUserSeeder.php
    ├── DatabaseSeeder.php
    ├── DepartmentSeeder.php
    ├── EmployeeSeeder.php
    ├── HrManagerSeeder.php
    ├── LeaveTypeSeeder.php
    ├── PositionSeeder.php
    └── RoleSeeder.php

resources/views/
├── attendance/
├── dashboard.blade.php
├── departments/
├── employees/
├── layouts/
│   └── app.blade.php
├── leaves/
├── notifications/
├── payroll/
├── performance/
├── positions/
└── reports/

routes/
├── api.php
├── auth.php
└── web.php
```

---

## API endpoints

All API endpoints require a Sanctum bearer token. Obtain a token via the standard Sanctum token endpoint.

```
GET    /api/employees          List employees (paginated)
POST   /api/employees          Create employee
GET    /api/employees/{id}     Get employee detail
PUT    /api/employees/{id}     Update employee
DELETE /api/employees/{id}     Delete employee

GET    /api/attendance         List attendance records
POST   /api/attendance         Check in or check out (action: check_in | check_out)
GET    /api/attendance/{id}    Get single record
PUT    /api/attendance/{id}    Update record
DELETE /api/attendance/{id}    Delete record

GET    /api/leaves             List leave requests
POST   /api/leaves             Submit leave request
GET    /api/leaves/{id}        Get leave detail
PUT    /api/leaves/{id}        Approve or reject (status: approved | rejected)
DELETE /api/leaves/{id}        Cancel pending request

GET    /api/payroll            List payroll records
POST   /api/payroll            Generate payroll for an employee
GET    /api/payroll/{id}       Get payroll detail with items
PUT    /api/payroll/{id}       Update payroll status (draft | approved | paid)
DELETE /api/payroll/{id}       Delete draft payroll
```

---

## Configuration

### Changing the late threshold

The default late check-in threshold is 09:00. To change it, open `app/Http/Controllers/AttendanceController.php`:

```php
$lateThreshold = Carbon::today()->setTime(9, 0); // change 9 to your preferred hour
```

### Changing payroll allowance and deduction rates

Open `app/Services/PayrollService.php` and update the percentage multipliers:

```php
$allowances = [
    ['label' => 'Housing Allowance',   'amount' => round($basic * 0.20, 2)], // 20%
    ['label' => 'Transport Allowance', 'amount' => round($basic * 0.10, 2)], // 10%
];

$deductions = [
    ['label' => 'Income Tax (10%)',     'amount' => round($basic * 0.10, 2)], // 10%
    ['label' => 'Social Security (5%)', 'amount' => round($basic * 0.05, 2)], // 5%
];
```

### Mail configuration for email notifications

To send email notifications in addition to database notifications, update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=hr@yourcompany.com
MAIL_FROM_NAME="HR Management System"
```

Then add `'mail'` to the `via()` array in each notification class.

---

## Future improvements

- **Recruitment module** — job postings, applicant tracking, interview scheduling, and offer letter generation that feeds directly into employee onboarding
- **Training and LMS** — course catalogue, enrollment, progress tracking, and completion certificates
- **Employee self-service portal** — document uploads, emergency contact management, bank account updates
- **Biometric integration** — REST webhooks from ZKTeco or similar devices to auto-populate attendance records
- **Mobile app** — Flutter or React Native front-end consuming the existing Sanctum API
- **Shift and overtime management** — shift schedules, rotating patterns, and overtime calculation fed into payroll
- **Document management** — contract versioning, digital signatures, and automated renewal reminders
- **Multi-company support** — tenant isolation for managing multiple companies from a single installation
- **Two-factor authentication** — TOTP-based 2FA for admin and HR manager accounts

---

## License

This project is open-sourced under the [MIT license](LICENSE).

---

## Contributing

Pull requests are welcome. For major changes please open an issue first to discuss what you would like to change.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/your-feature`)
3. Commit your changes (`git commit -m 'Add your feature'`)
4. Push to the branch (`git push origin feature/your-feature`)
5. Open a pull request

---

Built with Laravel 11 · PHP 8.2 · TailwindCSS · MySQL
