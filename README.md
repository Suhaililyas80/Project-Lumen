# Project-lumen

## Overview

This backend provides authentication, user management, task management, analytics, and notification APIs for your application.

---

## Framework & Design

-   **Framework:** Lumen (micro-framework by Laravel 6.x)
-   **Architecture:** Model-View-Controller (MVC) with a Services layer
-   **Language:** PHP 7.4
-   **Authentication:** Token-based (JWT), using `Authorization: Bearer <token>` in headers
-   **Database:** MySQL

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

## APIs

-   Auth: https://suhaililyas.postman.co/workspace/Suhail-Ilyas's-Workspace~4412f15c-54f4-494c-862c-32a4bd5b7f8d/collection/45605541-0223385a-6b11-42ab-9a16-a7445f114811?action=share&creator=45605541

-   Notificatio: https://suhaililyas.postman.co/workspace/Suhail-Ilyas's-Workspace~4412f15c-54f4-494c-862c-32a4bd5b7f8d/collection/45605541-e2b05127-fffb-4961-abd6-78768126671d?action=share&creator=45605541

-   TaskAnalytics: https://suhaililyas.postman.co/workspace/Suhail-Ilyas's-Workspace~4412f15c-54f4-494c-862c-32a4bd5b7f8d/collection/45605541-cbace1ed-1ca0-488e-9879-463840f49edd?action=share&creator=45605541

-   TaskManagement: https://suhaililyas.postman.co/workspace/Suhail-Ilyas's-Workspace~4412f15c-54f4-494c-862c-32a4bd5b7f8d/collection/45605541-7f5ee341-c986-4882-8076-9df989d51958?action=share&creator=45605541

-   User: https://suhaililyas.postman.co/workspace/Suhail-Ilyas's-Workspace~4412f15c-54f4-494c-862c-32a4bd5b7f8d/collection/45605541-56b0ffec-ac7a-4e90-97db-23ad6bc31656?action=share&creator=45605541

## Controllers

-   Act as entry points for HTTP requests
-   Responsible for:
    -   Validating incoming data
    -   Calling appropriate service classes for business logic
    -   Returning JSON responses for the frontend

---

## Services

-   Hold the core business logic
-   Called by controllers when data needs to be manipulated

---

## Models

-   Represent database tables and data logic (via Eloquent ORM)
-   Define relationships

---

## Routes

-   Define API endpoints and map them to controllers

---

## Running the Project

```bash
php -S localhost:8000 -t public
```
