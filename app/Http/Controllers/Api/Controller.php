<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * 成功响应
     */
    protected function success($data = null, string $message = '操作成功', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * 错误响应
     */
    protected function error(string $message = '操作失败', int $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
        ], $code);
    }
}
