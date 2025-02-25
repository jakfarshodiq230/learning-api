<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

/**
 * Class BaseController
 *
 * This class serves as a base controller for API responses.
 * It provides methods to send standardized success and error responses.
 */
class BaseController extends Controller
{
    /**
     * Send a success response.
     *
     * @param mixed $result The data to be returned in the response.
     * @param string $message A message indicating the success of the operation.
     * @return \Illuminate\Http\Response A JSON response containing the success status, data, and message.
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    /**
     * Return an error response.
     *
     * @param string $error The error message to be returned.
     * @param array $errorMessages Optional additional error details.
     * @param int $code The HTTP status code for the response (default is 404).
     * @return \Illuminate\Http\Response A JSON response containing the error status and message.
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
