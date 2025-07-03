# Project-lumen

## Overview
This backend provides authentication, user management, task management, analytics, and notification APIs for your application.

---

## Framework & Design

- **Framework:** Lumen (micro-framework by Laravel 6.x)
- **Architecture:** Model-View-Controller (MVC) with a Services layer
- **Language:** PHP 7.4
- **Authentication:** Token-based (JWT), using `Authorization: Bearer <token>` in headers
- **Database:** MySQL

---

## Folder Structure

```
be/
├── app/
│   ├── Console/
│   │   ├── Kernel.php
│   │   └── Commands/
│   │       └── SendTaskReminder.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── UserController.php
│   │   │   ├── TaskManagementController.php
│   │   │   ├── TaskAnalyticsController.php
│   │   │   ├── NotificationController.php
│   │   │   └── ExampleController.php
│   │   └── Middleware/
│   │       ├── Authenticate.php
│   │       └── CorsMiddleware.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── TaskManagement.php
│   │   ├── Notification.php
│   │   ├── Idrole.php
│   │   └── UserActivity.php
│   └── Services/
│       ├── AuthService.php
│       ├── NotificationService.php
│       ├── TaskAnalytics.php
│       ├── TaskService.php
│       ├── UserActivitys.php
│       └── UserService.php
├── bootstrap/
│   └── app.php
├── config/
│   ├── app.php
│   ├── jwt.php
│   ├── mail.php
│   └── broadcasting.php
├── resources/
│   └── views/
│       └── emails/
│           ├── confirm.blade.php
│           ├── reset.blade.php
│           ├── task_notification.blade.php
│           └── task_reminder.blade.php
├── routes/
│   └── web.php
├── .env
└── Dockerfile
```

---

## Controllers

- Act as entry points for HTTP requests
- Responsible for:
  - Validating incoming data
  - Calling appropriate service classes for business logic
  - Returning JSON responses for the frontend

---

## Services

- Hold the core business logic
- Called by controllers when data needs to be manipulated

---

## Models

- Represent database tables and data logic (via Eloquent ORM)
- Define relationships

---

## Routes

- Define API endpoints and map them to controllers

---

## Running the Project

```bash
php -S localhost:8000 -t public
```
