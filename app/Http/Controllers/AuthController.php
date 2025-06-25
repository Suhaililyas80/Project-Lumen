<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Services\UserActivitys;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    // Login and return JWT token

    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;//dependency injection of AuthService
    }

    //Login method
    public function login(Request $request)
{        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }
        // Ensure the request has 'email' and 'password' fields
        if (!$request->has(['email', 'password'])) {
            return response()->json(['success' => false, 'message' => 'Email and password are required'], 400);
        }

        $credentials = $request->only('email', 'password');
        $response = $this->authService->login($credentials);


        return response()->json($response, $response['status']);
    }

    // Logout method
    public function logout(Request $request)
    {
        // Call the service to handle logout
        $result = $this->authService->logout();
        return response()->json($result, $result['status']);
    }

    // Register a new user
    public function register(Request $request)
    {
       // $data = $request->only('name', 'email', 'password', 'password_confirmation');
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 422);
        }
        // Ensure the request has 'name', 'email', and 'password' fields
        if (!$request->has(['name', 'email', 'password'])) {
            return response()->json(['success' => false, 'message' => 'Name, email, and password are required'], 400);
        }
        $validdatedData = $validator->validated();
        //$credentials = $request->only('name','email','password');
        $response = $this->authService->register($validdatedData);
        return response()->json($response, $response['status']);
    }  

    // Email verification
    public function sendVerificationEmail(Request $request)
    {
        // You can use request data or hardcode for now
        //validate the email
        // $this->validate($request, [
        //     'email' => 'required|email|exists:users,email',
        // ]);

        $User = User::where('email', $request->input('email'))->first();
        if (!$User) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
        
        $result = $this->authService->sendVerificationEmail($User);
        return response()->json($result, $result['status']);
    
    }
    
   // Verify email by token
    public function verifyEmailbyToken(Request $request)
    {
        // Validate the token
        $this->validate($request, [
            'token' => 'required|string',
        ]);

        // Call the service
        $result = $this->authService->verifyEmailByToken($request->input('token'));
        return response()->json($result, $result['status']);
    }


    //forgot password
    public function forgotPassword(Request $request){
        // Validate the request
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
        ]);
        // $user = User::where('email', $request->input('email'))->first();
        $result = $this->authService->sendPasswordResetEmail($request->input('email'));
        return response()->json($result, $result['status']);
    }
    
    // reset password
    public function resetPassword(Request $request)
    {
        // Validate the request
        $this->validate($request, [
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        // Call the service
        $result = $this->authService->resetPassword($request->input('token'), $request->input('password'));
        return response()->json($result, $result['status']);
    }

    public function softDeleteUser(Request $request)
    {
        // Validate the request
        $this->validate($request, [
            'id' => 'required|exists:users,id',
        ]);

        // Call the service
        $result = $this->authService->softDeleteUser($request->input('id'));
        return response()->json($result, $result['status']);
    }

}