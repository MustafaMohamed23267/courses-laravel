<?php

namespace App\Helper;

class ApiResponse
{
    public static function success($data = [], $message = 'Success', $status = 200)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
            'error' => null,
        ], $status);
    }

    public static function error($message = 'Error', $error = [], $status = 400)
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'message' => $message,
            'error' => $error
        ], $status);
    }
}
