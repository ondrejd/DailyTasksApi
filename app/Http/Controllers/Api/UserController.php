<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="Users API",
 * )
 */
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
     * 
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
            return $this->sendError(__('Validation error'), $validator->errors()->toArray());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, __('User register successfully.'));
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
     *      @OA\Response(
     *          response="200",
     *          description="User successfully login",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="success",
     *                      type="bool",
     *                      example=true,
     *                  ),
     *                  @OA\Property(
     *                      property="data",
     *                      type="object",
     *                      @OA\Property(
     *                          property="token",
     *                          type="string",
     *                          example="4|xwf3ePQY97AJDiLLfDrwrK01hRshR12dZcBCLMms72515fcf",
     *                      ),
     *                      @OA\Property(
     *                          property="name",
     *                          type="string",
     *                          example="Test User",
     *                      ),
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="User successfully logged in",
     *                  ),
     *              )
     *          )
     *      ),
     *      @OA\Response(response="401", ref="#/components/responses/Unauthenticated"),
     * )
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

            return $this->sendResponse($success, __('User successfully logged in'));
        } else {
            return $this->sendError(__('Not authenticated'), [], 401);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/user/info",
     *      summary="User info",
     *      tags={"Users"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response="200",
     *          description="Logged user info",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="success",
     *                      type="bool",
     *                      example=true,
     *                  ),
     *                  @OA\Property(
     *                      property="data",
     *                      type="array",
     *                      @OA\Items(ref="#/components/schemas/User"),
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="",
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response="401", ref="#/components/responses/Unauthenticated"),
     * )
     */
    public function info(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->sendResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            // TODO Množství úkolů a tagů
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/user/unregister",
     *      summary="Unregister logged user",
     *      description="User will be unregistred and __deleted__ thus __all its data will be erased__.",
     *      tags={"Users"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(response="200", description="User deleted successfully"),
     *      @OA\Response(response="401", ref="#/components/responses/Unauthenticated"),
     * )
     */
    public function unregister(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->delete();
        $user->delete();

        return $this->sendResponse([], __('User deleted successfully'));
    }
}
