<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * 获取当前登录用户信息
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return $this->success($request->user(), '获取用户信息成功');
    }

    /**
     * 获取用户列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $users = User::paginate($request->get('per_page', 15));

        return $this->paginate($users, '获取用户列表成功');
    }
}
