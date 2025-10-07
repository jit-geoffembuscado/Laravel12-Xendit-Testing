<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
    public function sendResponse($result, $message = '', $code = 200)
    {
        $response = [
            'status' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    public function sendError($result, $message = '', $code = 400)
    {
        $response = [
            'status' => false,
            'data'    => (object) $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }
}
