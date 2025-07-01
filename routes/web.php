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


// $router->get('/check', function (){
//     return app('db')->select("SELECT 2 as Test");
// });


$router->group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () use ($router) {

    $router->post('login', 'AuthController@login');
    $router->post('logout', 'AuthController@logout');
    $router->post('refresh', 'AuthController@refresh');
    $router->post('me', 'AuthController@me');

});


//Required api routes
$router->post('auth/register', 'AuthController@register');
$router->post('auth/logout', 'AuthController@logout');
$router->post('auth/login', 'AuthController@login');
$router->post('auth/send-verification-email', 'AuthController@sendVerificationEmail');
$router->post('auth/verify-email', 'AuthController@verifyEmailbyToken');
$router->post('auth/forgot-password', 'AuthController@forgotPassword');
$router->post('auth/reset-password', 'AuthController@resetPassword');

$router->post('auth/softDeleteUser', 'AuthController@softDeleteUser');

$router->post('/auth/assign-role', 'UserController@assignRole');

$router->post('auth/user-listing', 'UserController@userlist');
// $router->post('auth/get-all-admin','UserController@getalladmins');
$router->post('auth/multiple-user-delete', 'UserController@multipleUserDelete');
// $router->post('auth/get-all-loggedinusers','UserController@getallloggedinusers');
$router->post('auth/get-all-user-activities', 'UserController@userActivities');
$router->post('auth/get-user-detail', 'UserController@getUserDetail');

$router->group([
    'middleware' => 'auth:api',
    'prefix' => 'auth'
], function () use ($router) {
    $router->post('create-task', 'TaskManagementController@createTask');
    $router->post('update-task/{taskId}', 'TaskManagementController@updateTask');
    $router->post('update-task-status/{taskId}', 'TaskManagementController@updateTaskStatus');
    $router->post('get-tasks', 'TaskManagementController@getTasks');
    $router->post('get-task-detail/{taskId}', 'TaskManagementController@getTaskDetail');
    $router->post('get-number-of-tasks-bystatus', 'TaskAnalyticsController@getNumberOfTasksByStatus');
    $router->post('get-tasks-duetoday', 'TaskAnalyticsController@getTasksDueToday');
    $router->post('notify-user', 'TaskNotificationController@notifyuser');
    $router->post('count-notification-of-user', 'NotificationController@countnotificationofuser');
    $router->post('get-all-notifications', 'NotificationController@getallnotifications');
});
