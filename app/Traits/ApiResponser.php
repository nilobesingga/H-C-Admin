<?php

namespace App\Traits;

trait ApiResponser
{
    protected function successResponse($message, $data = null, $code = 200)
    {
        return response()->json(['status' => 'success', 'message' => $message, 'data' => $data], $code);
    }

    protected function errorResponse($message, $exception = null, $code = 500)
    {
        return response()->json(['status' => 'error', 'message' => $message, 'exception' => $exception], $code);
    }
}
