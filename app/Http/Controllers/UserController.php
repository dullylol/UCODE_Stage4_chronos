<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Exception;

class UserController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('login', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $exception) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'login' => $request->get('login'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }

    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $exception) {
            return response()->json(['token_expired'], 400);

        } catch (TokenInvalidException $exception) {
            return response()->json(['token_invalid'], 400);
        } catch (JWTException $exception) {
            return response()->json(['token_absent'], 404);
        }

        return response()->json(compact('user'));
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'successfully_logged_out']);
        } catch (TokenInvalidException $exception) {
            return response()->json(['JWT_error' => $exception->getMessage()], 401);
        } catch (JWTException $exception) {
            return response()->json(['JWT_error' => $exception->getMessage()], 401);
        }
    }


    public function all()
    {
        if (!$user = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['message' => 'user_is_not_authentificate'], 400);
        }

        return User::all();
    }

    public function create(Request $request)
    {
        if (!$user = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['message' => 'user_is_not_authentificate'], 400);
        }

        try {
            return User::create($request->all());
        } catch (Exception $exception) {
            return response()->json(['message' => 'incorrect_request_data'], 400);
        }
    }

    public function byId($id)
    {
        if (!$user = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['message' => 'user_is_not_authentificate'], 400);
        }

        $userById = User::find($id);

        if (!$userById) {
            return response()->json(['message' => 'no_such_user'], 404);
        }

        return response()->json($userById, 200);
    }

    public function update(Request $request, $id)
    {
        if (!$user = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['message' => 'user_is_not_authentificate'], 400);
        }

        if ($user['id'] != $id) {
            return response()->json(['message' => 'user_cannot_edit_another_users'], 400);
        }

        $correctRequest = $request->only('login', 'email', 'password');

        $updater = [];
        try {
            if (isset($correctRequest['login'])) {
                $updater['login'] = $correctRequest['login'];
            }
            if (isset($correctRequest['email'])) {
                $updater['email'] = $correctRequest['email'];
            }
            if (isset($correctRequest['password'])) {
                $updater['password'] = Hash::make($correctRequest['password']);
            }
            $user->update($updater);

        } catch (Exception $exception) {
            return response()->json(['message' => 'incorrect_request_data'], 400);
        }

        return $user;
    }

    public function destroy($id)
    {
        if (!$user = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['message' => 'user_is_not_authentificate'], 400);
        }

        if ($user['id'] != $id) {
            return response()->json(['message' => 'user_cannot_delete_another_users'], 400);
        }

        User::destroy($id);

        return response()->json(['message' => 'user_successful_deleted'], 200);
    }

}
