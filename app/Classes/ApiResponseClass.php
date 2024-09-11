<?php

namespace App\Classes;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponseClass
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    public static function rollback($e, $message = "Something went wrong! Process not completed")
    {
        DB::rollBack();
        self::throw($e, $message);
    }

    public static function throw($e, $message = "Something went wrong! Process not completed")
    {
        Log::info($e);
        throw new HttpResponseException(response()->json(["message" => $message]));
    }

    public static function sendResponse($result, $message, $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $result
        ];

        if (!empty($message)) {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }
}
