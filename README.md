#Project-lumen
#Overview
This backend provides authentication, user management, task management, analytics, and notification APIs for application.
#Framework & Design
-Framework -> Lumen(micro-framework by laravel.6x)
-Architecture -> Model-View-Controller with Services layer
-Language -> php 7.4
-Authentication -> Token-based (JWT), using Authorization: Bearer<token>in headers
-Database: MySQL
#Folder structure
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
├── resources/views
│       └── emails/
│           ├── confirm.blade.php
│           ├── reset.blade.php
│           ├── task_notification.blade.php
│           └── task_reminder.blade.php
├── routes/
│   └── web.php
├── .env
└── Dockerfile

#Controller
-act as entry points for HTTP requests
-responsible for:
  -Validating incoming data
  -calling appropriate service classes for business logic
  -return JSON responses for frontend
#Services 
-hold the core business logic
-called by controller when the data needs to be manipulated 
#Models
-represent database tables and data logic(via Eloquent ORM)
-define relationships
#Routes
-Define API endpoints and map them to controller
#Running the Project
```bash
php -S localhost:8000 -t public
```
