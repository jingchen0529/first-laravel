<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * 通知列表
     */
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Notification/Index', [
            'list' => $notifications,
            'unreadCount' => Notification::where('user_id', $request->user()->id)
                ->whereNull('read_at')
                ->count(),
        ]);
    }

    /**
     * 标记单条为已读
     */
    public function read(Request $request, $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $notification->markAsRead();

        return back()->with('success', '已标记为已读');
    }

    /**
     * 全部标记为已读
     */
    public function readAll(Request $request)
    {
        Notification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', '全部已标记为已读');
    }

    /**
     * 删除通知
     */
    public function destroy(Request $request, $id)
    {
        Notification::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->delete();

        return back()->with('success', '删除成功');
    }
}
