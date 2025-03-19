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
     * @todo Just temporary will be remade when we have front-end and sent passwords will be already bcrypted
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
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
            $success['token'] =  $user->createToken(env('APP_NAME'))->plainTextToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function info(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->sendResponse([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function delete(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->delete();
        $user->delete();

        return $this->sendResponse([], 'User deleted successfully.');
    }
}
