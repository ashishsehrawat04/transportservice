<?php

namespace App\Helpers;

class Response
{
    public static function success($data = [], $message = 'Success', $code = 200)
    {
        return response()->json([
            'status'  => true,
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    public static function error($message = 'Error', $code = 500, $data = null)
    {
        return response()->json([
            'status'  => false,
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }
}
