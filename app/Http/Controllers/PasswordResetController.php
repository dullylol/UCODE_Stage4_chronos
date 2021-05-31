<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;

class PasswordResetController extends Controller
{
    public function ForgotPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::all()->where('email', $request->input('email'))->first();
        $token = Hash::make(Str::random(20));

        try {
            if (PasswordReset::all()->where('email', $user->email)->first()) {
                PasswordReset::where('email', $user->email)->update(['token' => $token]);
            } else {
                PasswordReset::create([
                    'email' => $user->email,
                    'token' => $token,
                ]);
            }

            $protocol = explode('//', $request->header('referer'))[0];
            $host = explode('//', $request->header('referer'))[1];
            $data = [
                'login' => $user->login,
                'resetLink' => $protocol . '//' . $host . 'forgot-password/' . $token,
                'removeLink' => $protocol . '//' . $host . 'forgot-password/' . $token . '/remove',
            ];
            Mail::send('forgot', $data, function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Password reset confirmation');
            });

            return response([
                'message' => 'password_reset_confirmation_sent_to_' . $user->email,
            ]);
        } catch (Exception $exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }

    public function ResetPassword(Request $request, mixed $token)
    {

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        try {
            if (!$data = PasswordReset::all()->where('token', $token)->first()) {
                return response([
                    'message' => 'invalid_token',
                ], 400);
            }

            if (!$user = User::all()->where('email', $data->email)->first()) {
                return response([
                    'message' => 'user_does_not_exist!',
                ], 404);
            }

            $user->password = Hash::make($request->input('password'));
            $user->save();

            PasswordReset::where('email', $data->email)->delete();
        } catch (\Exception$exception) {
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }

        return response([
            'message' => 'password_reset_successful',
        ]);
    }
    public function RemoveRequestPassword(mixed $token)
    {
        if (!$data = PasswordReset::where('token', $token)->first()) {
            return response([
                'message' => "password_reset_token_not_found",
            ]);
        }

        $data->delete();
        return response([
            'message' => "password_reset_token_was_successfully_deleted",
        ]);
    }
}
