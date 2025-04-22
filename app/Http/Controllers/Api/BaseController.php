<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *      title="DailyTasks API",
 *      version="1.0.0",
 * )
 * @OA\Server(
 *      url="http://localhost:8000",
 *      description="API server (development)",
 * )
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      name="bearerAuth",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 *      in="header",
 * )
 * @OA\Response(
 *     response="Unauthenticated",
 *     description="User not aunthenticated",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="success",
 *                 type="bool",
 *                 example=false,
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Not authenticated",
 *             ),
 *         )
 *     )
 * ),
 * @OA\Response(
 *     response="Unauthorized",
 *     description="User unauthorized",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="success",
 *                 type="bool",
 *                 example=false,
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Not authorized",
 *             ),
 *         )
 *     )
 * ),
 * @OA\Response(
 *     response="NotFound",
 *     description="Target model not found",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="success",
 *                 type="bool",
 *                 example=false,
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Not found",
 *             ),
 *         )
 *     )
 * ), 
 * @OA\Response(
 *     response="ValidationError",
 *     description="Validation error",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="success",
 *                 type="bool",
 *                 example=false,
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 description="Contains array with keys of fields names (such `name`, `email`, `color` etc. according to input data) with an array of errors which was found",
 *                 type="object",
 *                 @OA\Property(
 *                     property="name",
 *                     description="Name of field where the validation errors occured",
 *                     type="array",
 *                     @OA\Items(type="string", example="The name has already been taken"),
 *                 ),
 *             ),
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Validation error",
 *             ),
 *         ),
 *     ),
 * ),
 */
class BaseController extends Controller
{
    /**
     * Success response method.
     *
     * @return JsonResponse
     */
    public function sendResponse(array|null $result, string $message = ''): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($result !== null) {
            $response['data'] = $result;
        }
  
        return response()->json($response, 200);
    }
  
    /**
     * Return error response.
     *
     * @return JsonResponse
     */
    public function sendError(string $error, array $errorMessages = [], int $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];
  
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
  
        return response()->json($response, $code);
    }
}