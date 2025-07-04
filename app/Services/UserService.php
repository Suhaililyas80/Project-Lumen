<?php

namespace App\Services;
use App\Models\User;
use App\Models\IdRole;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserService
{
    //Assign role to user
    public function assignRole($userId, $roleName)
    {
        $user = User::find($userId);
        if (!$user) {
            // Response""error(\Exception('User not found');
            return [
                'success' => false,
                'message' => 'User not found',
                'status' => 404,
            ];
        }
        // Find or create the role
        $role = IdRole::firstOrCreate(['role' => $roleName]);
        // Update user's role_id
        $user->role_id = $role->role_id;
        $user->save();
        // Update roleuser pivot table
        $user->roles()->syncWithoutDetaching([$role->role_id]);
        return [
            'success' => true,
            'message' => 'Role assigned successfully',
            'status' => 200,
        ];

    }

    // public function Userlist

    public function userlisting(array $filters = [], $page = 1, $perPage = 10)
    {
        $query = User::query();
        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }
        if (isset($filters['email'])) {
            $query->where('email', $filters['email']);
        }
        if (isset($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('role', 'like', '%' . $filters['role'] . '%');
            });
        }
        //implement total count
        $users = $query->get();
        //add role to user, createdby-name
        $users = $users->map(function ($user) {
            $user->role_names = $user->roles->pluck('role')->join(',');
            $user->createdby = $user->creator->name ?? null;
            return $user;
        });
        return [
            'success' => true,
            'message' => 'User listing retrieved successfully',
            'users' => $users,
            'status' => 200,
        ];
    }


    public function multipleUserDelete($userIds)
    {
        $deletedCount = 0;
        $notfoundCount = 0;
        $foundids = [];
        $authUser = Auth::user();
        $authUserRole = IdRole::find($authUser->role_id);
        if (!$authUserRole || strtolower($authUserRole->role) !== 'admin') {
            return [
                'success' => false,
                'message' => 'You do not have permission to delete these users',
                'status' => 403,
            ];
        }
        $users = User::whereIn('id', $userIds)->get();
        foreach ($users as $user) {
            if ($user) {
                if ($authUser && $user->id === $authUser->id) {
                    continue;
                }
                $deletedCount++;
                $foundids[] = $user->id;
            }
        }
        $notfoundCount = count($userIds) - $deletedCount;
        try {
            DB::beginTransaction();
            // Soft delete the users by admin
            User::whereIn('id', $foundids)->update(['deleted_by' => $authUser->id]);
            User::whereIn('id', $foundids)->delete();
            // Commit the transaction   
            DB::commit();
            return [
                'success' => true,
                'message' => "$deletedCount users deleted successfully, $notfoundCount users not found",
                'status' => 200,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Error deleting users: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }
}