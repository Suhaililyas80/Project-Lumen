<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Services\UserActivitys;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Services\UserService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller {


protected $userService;
    public function __construct()
    {
        // Dependency injection of AuthService
        $this->userService = new UserService();
    }


    // assign role to user method
    public function assignRole(Request $request)
    {
        // Validate the request
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:idrole,role',
        ]);
        // Call the service to assign role
        $response = $this->userService->assignRole($request->input('user_id'), $request->input('role'));
        return response()->json($response, $response['status']); // response formatting
    }
    //userlist method
    public function userlist(Request $request)
    {   // Validate the request
        // Call the service
        $this->validate($request, [
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
            'name' => 'string|exists:users,name|nullable',
            'email' => 'email|exists:users,email|nullable',
            'role' => 'string|exists:idrole,role|nullable',
        ]);

        $filters = [];
        $filters['name'] = $request->input('name');
        $filters['email'] = $request->input('email');
        $filters['role'] = $request->input('role');

        //for pagination
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $users = $this->userService->userlisting($filters, $page, $perPage);

        return response()->json($users, 200); // response formatting
    }

    public function getalladmins(Request $request){
        $this->validate($request, [
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
            'role' => 'string|exists:idrole,role|nullable', // Assuming 'admin' is a role in the roles table
        ]);

        $filters = [];      
        $filters['role'] = $request->input('role'); // Assuming 'admin' is a filter for admin users
        //for pagination
        $page = $request->input('page',1);  
        $perPage = $request->input('',10);
        $admins = $this->userService->userlisting($filters, $page, $perPage);
        return response()->json($admins, 200); // response formatting
    }

    public function multipleUserDelete(Request $request)
    {
        // Validate the request
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);         
        // Call the service to delete multiple users
        $response = $this->userService->multipleUserDelete($request->input('ids'));
        return response()->json($response, $response['status']); // response formatting
    }

    public function getallloggedinusers(Request $request)
    {
        // Call the service to get all logged-in users
        $activity = new UserActivitys();
        $loggedInUsers = $activity->getalllogedinuser();
        return response()->json($loggedInUsers, 200); // response formatting
    }
    // public function index(){
    //     $users =User::all();
    //     return response()->json([
    //         'success'=>true,
    //         'users'=> $users
    //     ],200);
    // }


    /// list user activities
     public function userActivities(Request $request){
        //if i am passing no parameters, it will return all activities
        //if i am passing user_id, it will return activities of that user
        //if i am passing email, it will return activities of that user
        //if i am passing role, it will return activities of that user
        // validate the request
        $this->validate($request, [
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
            // 'user_id' => 'integer|exists:users,id|nullable',
            'user_id' => 'integer|exists:users,id|nullable',
            'email' => 'email|exists:users,email|nullable',
            'role' => 'string|exists:idrole,role|nullable',
        ]);
        $filters = [];
        $filters['user_id'] = $request->input('user_id');
        $filters['email'] = $request->input('email');
        $filters['role'] = $request->input('role');
        //for pagination
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        //call the user activity service
        $activity = new UserActivitys();
        $activities = $activity->getUserActivity($filters, $page, $perPage);
        return response()->json($activities, 200); // response formatting
     }
}