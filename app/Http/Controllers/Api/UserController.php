<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class UserController extends BaseController
{
    /**
     * @OA\Post(
     *      path="/api/user/register",
     *      summary="Register new user",
     *      tags={"Users"},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      description="User's name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      description="User's email",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      description="User's password",
     *                      type="string"
     *                  ),
     *                  required={"name", "email", "password"},
     *                  example={"name": "Jane Doe", "email": "jane.doe@mail.com", "password": "abcdef"}
     *              )
     *          )
     *      ),
     *      @OA\Response(response="201", description="User registered successfully"),
     *      @OA\Response(response="422", description="Validation errors")
     * )
     * @todo Just temporary will be remade when we have front-end and sent passwords will be already bcrypted
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors()->toArray());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * @OA\Post(
     *      path="/api/user/login",
     *      summary="User login",
     *      tags={"Users"},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="email",
     *                      description="User's email",
     *                      type="string",
     *                      example="test@example.com"
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      description="User's password",
     *                      type="string",
     *                      example="password"
     *                  ),
     *                  required={"email", "password"}
     *              )
     *          )
     *      ),
     *      @OA\Response(response="200", description="User successfully login"),
     *      @OA\Response(response="404", description="Unauthorised")
     * )
     * 
     * @todo Just temporary will be remade when we have front-end and sent password will be already bcrypted
     */
    public function login(Request $request): JsonResponse
    {
        $attempt = Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($attempt) {
            $user = Auth::user();
            // TODO Negenerovat pořád nový, ale použít starší, který neexpiroval
            $success['token'] =  $user->createToken(env('APP_NAME'))->plainTextToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 'User successfully login');
        } else {
            return $this->sendError('Unauthorised', ['error' => 'Unauthorised']);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/user/info",
     *      summary="User info",
     *      tags={"Users"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(response="200", description="Logged user info"),
     *      @OA\Response(response="400", description="Unauthorised")
     * )
     */
    public function info(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->sendResponse([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/user/unregister",
     *      summary="Unregister logged user",
     *      description="User will be unregistrated - all its data will be erased.",
     *      tags={"Users"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(response="200", description="User deleted successfully"),
     *      @OA\Response(response="400", description="Unauthorised")
     * )
     */
    public function unregister(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->delete();
        $user->delete();

        return $this->sendResponse([], 'User deleted successfully');
    }
}
