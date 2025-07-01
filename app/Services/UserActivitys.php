<?php 

namespace App\Services;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserActivitys
{

    public function logLogin()
    {
        $user = Auth::user();
        if ($user) {
            UserActivity::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'login_time' => Carbon::now(),
            ]);
        }
        return null;
    }

    public function logLogout(){
        
        $user = Auth::user();
        if ($user) {
            $activity = UserActivity::where('user_id', $user->id)
                ->whereNull('logout_time')
                ->orderBy('login_time', 'desc')
                ->first();

            if ($activity) {
                $activity->logout_time = Carbon::now();
                $activity->duration = $activity->logout_time->diffInSeconds($activity->login_time);
                $activity->save();
            }
        }
        return null;
    }

    public function getalllogedinuser(){
        $activities = UserActivity::whereNull('logout_time')
            ->orderBy('login_time', 'desc')
            ->get();
        return $activities;
    }
    public function getUserActivity(array $filters,$page = 1, $perPage = 10)
    {
        $query = UserActivity::query();
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (isset($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }
        if (isset($filters['role'])) {
            $query->whereHas('user.roles', function ($q) use ($filters) {
                $q->where('role', 'like', '%' . $filters['role'] . '%');
            });
        }
        $userActivities = $query->get();
        return [
            'success' => true,
            'message' => 'User activities retrieved successfully',
            'activities' => $userActivities,
            'status' => 200,
        ];
    }
}