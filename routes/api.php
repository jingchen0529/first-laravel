<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API 路由
|--------------------------------------------------------------------------
|
| 所有路由自动带 /api 前缀
| 例如: Route::get('/users') 实际访问地址是 /api/users
|
*/

// 公开接口（无需登录）
Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to API',
        'version' => 'v1',
    ]);
});

// 需要认证的接口
Route::middleware('auth:sanctum')->group(function () {
    // 当前用户
    Route::get('/user', [UserController::class, 'me']);

    // 用户管理
    Route::get('/users', [UserController::class, 'index']);
});
