<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class Controller extends BaseController
{
    /**
     * 操作成功
     */
    protected function success(string $msg = '操作成功', mixed $data = null, int $code = 1): void
    {
        $this->result($msg, $data, $code);
    }

    /**
     * 操作失败
     */
    protected function error(string $msg = '操作失败', mixed $data = null, int $code = 0): void
    {
        $this->result($msg, $data, $code);
    }

    /**
     * 返回 API 数据
     */
    protected function result(string $msg, mixed $data = null, int $code = 0, int $statusCode = 200, array $header = []): void
    {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => request()->server('REQUEST_TIME'),
            'data' => $data,
        ];

        $response = response()->json($result, $statusCode)->withHeaders($header);

        throw new HttpResponseException($response);
    }
}
