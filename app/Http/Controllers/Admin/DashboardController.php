<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        return Inertia::render('Dashboard', [
            'stats' => [
                'userCount' => User::count(),
                'todayUserCount' => User::whereDate('created_at', today())->count(),
                'notificationCount' => Notification::where('user_id', $userId)->count(),
                'unreadNotificationCount' => Notification::where('user_id', $userId)->whereNull('read_at')->count(),
            ],
            'recentNotifications' => Notification::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ]);
    }
}
