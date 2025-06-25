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
   // Step 1: Find or create the role
    $role = IdRole::firstOrCreate(['role' => $roleName]);

    // Step 2: Update user's role_id
    $user->role_id = $role->role_id;
    $user->save();

    // Step 3: Update roleuser pivot table
    // Make sure the roles() relationship is defined in User model
    $user->roles()->syncWithoutDetaching([$role->role_id]);
    return [
        'success' => true,
        'message' => 'Role assigned successfully',
        'status' => 200,
    ];
    //relation 
    //traits
}

  // public function Userlist

  public function userlisting(array $filters = [], $page = 1, $perPage = 10){
    $query = User::query();

    if (isset($filters['name'])) {
        $query->where('name', 'like', '%' . $filters['name'] . '%');
    }
    if (isset($filters['email'])) {
        $query->where('email',$filters['email']);
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
         $user->role_names= $user->roles->pluck('role')->join(',');
         $user->createdby= $user->creator ? $user->creator->name:null;
         return $user;});
    
    return [
        'success' => true,
        'message' => 'User listing retrieved successfully',
        'users' => $users,
        'status' => 200,
    ];
  }

  /* * Delete multiple users by IDs
  * @param array $userIds
  * @return array
  * @throws \Exception
  **/
   public function multipleUserDelete($userIds){
    $deletedCount =0;
    $notfoundCount = 0;
    $foundids=[];
    // get all users with the given IDs from the database at once
    $users = User::whereIn('id', $userIds)->get();
    // Loop through the users and get the count of deleted and not found users
    foreach ($users as $user) {
        if ($user) {
            $deletedCount++;
            $foundids[] = $user->id;
    }
    }
     $notfoundCount=count($userIds) - $deletedCount;
     $authUser = Auth::user();
     if (!$authUser) {   
            return [
                'success' => false,
                'message' => 'Unauthorized: You must be logged in to delete users',
                'status' => 401,
            ];
        }
        
     $authUserRole = IdRole::find($authUser->role_id);
     if (!$authUserRole || strtolower($authUserRole->role) !== 'admin') {
         return [
             'success' => false,
             'message' => 'You do not have permission to delete these users',
             'status' => 403,
         ];
     }
    try{
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