<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Namshi\JOSE\JWT;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\IdRole;
use App\Services\UserActivitys;
use App\Services\UserService;
// Use the UserActivitys service to log user activities

class AuthService
{
    // Login and return JWT token
    public function login($credentials)
    {

        $user = User::where('email', $credentials['email'])->first();
        if ($user->email_verified_at === null) {
            return [
                'success' => false,
                'message' => 'Please Verify',
                'status' => 401, // or 403
            ];
        }

        if (!$token = Auth::attempt($credentials)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials',
                'status' => 401,

            ];
        }
        if ($user->email_verified_at !== null) {
            // Log user activity
            //UserActivitys::logLogin();
            // call the UserActivitys service to log login activity
            // $serviceClass=\App\Services\UserActivitys::class;
            // $metthodName='logLogin';
            $activity = new UserActivitys();
            $activity->logLogin();

            return [
                'success' => true,
                'message' => 'Please Login now',
                'token' => $token,
                'status' => 200,
            ];
        }
    }

    // Logout method
    public function logout()
    {

        $acivity = new UserActivitys();
        // Call the UserActivitys service to log logout activity
        $acivity->logLogout();
        // Clear the JWT token
        //JWTAuth::invalidate(JWTAuth::getToken());
        // Use Auth facade to logout the user
        // Auth::logout();      

        Auth::logout();
        return [
            'success' => true,
            'message' => 'User logged out successfully',
            'status' => 200,
        ];
    }

    // Register a new user
    public function register($validdatedData)
    {

        $validdatedData['password'] = Hash::make($validdatedData['password']);

        $user = User::create($validdatedData);
        // Call email verification before registration
        //Generate a confirmation token
        $token = str::random(32);
        $user->confirmation_token = $token;
        $user->save();
        //authuser
        $authuser = Auth::user();

        //check if authuser is admin or not
        if ($authuser) {
            if (!$authuser->roles->contains('role', 'admin')) {
                return [
                    'success' => false,
                    'message' => 'Only admin can create users',
                    'status' => 403, // Forbidden
                ];
            } else {
                // If the user is created by an admin, set created_by to the admin's ID
                $user->created_by = $authuser->id;
            }
        } else {
            // If not, set created_by to null or handle as needed
            $user->created_by = null;
        }
        $user->save();
        // Assign default role if needed
        $UserService = new UserService();
        $UserService->assignRole($user->id, 'user'); // Assuming 'user' is the default role



        $emailResult = $this->sendVerificationEmail($user);

        return [
            'success' => true,
            'user' => $user,
            'status' => 201,
        ];
    }


    // send verification email
    public function sendVerificationEmail($user)
    {

        //$token = JWTAuth::fromUser($user);
        $verification_link = url('/verify-email?token=' . urlencode($user->confirmation_token));
        $data = [
            'name' => $user['name'],
            'email' => $user['email'],
            'verification_link' => $verification_link,
        ];


        Mail::send('emails.confirm', $data, function ($message) use ($user) {
            $message->to($user['email'], $user['name'])
                ->subject('Email Verification');
        });
        return [
            'success' => true,
            'message' => 'Verification email sent.',
        ];
    }

    public function verifyEmailbytoken($token)
    {
        $user = User::where('confirmation_token', $token)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid or expired token',
                'status' => 404,
            ];
        }
        // Update the user's email_verified_at timestamp
        $user->email_verified_at = Carbon::now();
        $user->confirmation_token = null; // Clear the token after verification
        $user->save();
        //user created by other loged user 


        return [
            'success' => true,
            'message' => 'Email verified successfully',
            'status' => 200,
        ];
    }

    //forget password
    public function sendPasswordResetEmail($email)
    {
        $user = User::Where('email', $email)->first();
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
                'status' => 404,
            ];
        }
        // generate password reset token
        $token = Str::random(32);
        $user->confirmation_token = $token; // Reusing confirmation_token for password reset
        $user->save();
        // Send reset password email
        $reset_link = url('/reset-password?token=' . urlencode($token));
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'reset_link' => $reset_link,
        ];
        Mail::send('emails.reset', $data, function ($message) use ($user) {
            $message->to($user->email, $user->name)
                ->subject('Password Reset');
        });
        return [
            'success' => true,
            'message' => 'Password reset email sent.',
            'status' => 200,
        ];
    }
    // Reset password using token
    public function resetPassword($token, $newPassword)
    {
        $user = User::where('confirmation_token', $token)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid or expired token',
                'status' => 404,
            ];
        }

        // Update the user's password
        $user->password = Hash::make($newPassword);
        $user->confirmation_token = null; // Clear the token after reset
        $user->save();

        return [
            'success' => true,
            'message' => 'Password reset successfully',
            'status' => 200,
        ];
    }

    public function softDeleteUser($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
                'status' => 404,
            ];
        }
        $authUser = Auth::user();
        if (!$authUser) {
            return [
                $user->deleted_by = null,
                $user->save(),
                $user->delete(),
                'success' => true,
                'message' => 'User delete itself',
                'status' => 401,
            ];
        }
        $authUserRole = IdRole::find($authUser->role_id);

        // Only allow if admin (by role name, not id)
        if (!$authUserRole || strtolower($authUserRole->role) !== 'admin' || $authUser->id === $user->id) {
            return [
                'success' => false,
                'message' => 'You do not have permission to delete this user',
                'status' => 403,
            ];
        }
        // Soft delete the user
        $user->deleted_by = $authUser->id ?? null;
        $user->save();
        $user->delete();

        return [
            'success' => true,
            'message' => 'User deleted successfully',
            'status' => 200,
        ];
    }
    // Get user by ID
}