<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    //return $router->app->version();
    echo "Welcome to the Lumen!";
});


//Required api routes
$router->post('auth/register', 'AuthController@register');
$router->post('auth/login', 'AuthController@login');
$router->post('auth/logout', 'AuthController@logout');
$router->post('auth/send-verification-email', 'AuthController@sendVerificationEmail');
$router->post('auth/verify-email', 'AuthController@verifyEmailbyToken');
$router->post('auth/forgot-password', 'AuthController@forgotPassword');
$router->post('auth/reset-password', 'AuthController@resetPassword');

$router->group([
    'middleware' => 'auth:api',
    'prefix' => 'auth'
], function () use ($router) {
    // user management api routes
    $router->post('assign-role', 'UserController@assignRole');
    $router->post('user-listing', 'UserController@userlist');
    $router->post('multiple-user-delete', 'UserController@multipleUserDelete');
    $router->post('get-all-user-activities', 'UserController@userActivities');
    $router->post('get-user-detail', 'UserController@getUserDetail');
    // task management api routes
    $router->post('create-task', 'TaskManagementController@createTask');
    $router->post('update-task/{taskId}', 'TaskManagementController@updateTask');
    $router->post('update-task-status/{taskId}', 'TaskManagementController@updateTaskStatus');
    $router->post('get-tasks', 'TaskManagementController@getTasks');
    $router->post('get-task-detail/{taskId}', 'TaskManagementController@getTaskDetail');

    // task analytics api routes
    $router->post('get-number-of-tasks-bystatus', 'TaskAnalyticsController@getNumberOfTasksByStatus');
    $router->post('get-tasks-duetoday', 'TaskAnalyticsController@getTasksDueToday');

    // notification api routes
    $router->post('count-notification-of-user', 'NotificationController@countnotificationofuser');
    $router->post('get-all-notifications', 'NotificationController@getallnotifications');
    $router->post('mark-as-read/{notificationId}', 'NotificationController@markAsRead');
});
