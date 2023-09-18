<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContextErrorEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->only(['username', 'password']);
            $validate = Validator::make($credentials, ['username' => 'required', 'password' => 'required'], ['username.required' => 'Username harus di isi', 'password.required' => 'Password harus di isi']);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
            } else {
                $token = auth()->attempt($credentials);
                if ($token) {
                    return $this->responseSuccess([
                        'sip_point_token_app' => $token,
                        'expires_in' => auth()->factory()->getTTL() * 60,
                        'token_type' => 'Bearer'
                    ], 200, ['Authorization' => 'Bearer ' . $token]);
                } else {
                    return $this->responseError(ContextErrorEnum::INVALID_CREDENTIALS, [
                        'message' => 'Invalid username atau password'
                    ], 401);
                }
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, [
                'message' => 'Internal server error'
            ], 500);
        }
    }

    public function me()
    {
        try {
            // solve with give data key on array to object
            return $this->responseSuccess(['data' => auth()->user()]);
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, [
                'message' => 'Internal server error'
            ], 500);
        }
    }

    public function refresh()
    {
        try {
            $token = auth()->refresh();
            return $this->responseSuccess([
                'sip_point_token_app' => $token,
                'expires_in' => auth()->factory()->getTTL() * 60,
                'token_type' => 'Bearer'
            ], 200, ['Authorization' => 'Bearer ' . $token]);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
    public function logout()
    {
        try {
            auth()->logout();
            return $this->responseSuccess([
                'message' => 'success logout'
            ]);
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, [
                'message' => 'Internal server error'
            ], 500);
        }
    }
    public function updateProfile(Request $request)
    {
        try {
            $credentials = $request->only(['nama', 'username']);
            $validate = Validator::make($credentials, ['username' => 'required', 'nama' => 'required'], ['username.required' => 'Username harus di isi', 'nama.required' => 'Nama harus di isi']);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
            } else {
                $user = User::find(auth()->user()->id);
                $update = User::where('id', auth()->user()->id)->update(["nama" => $request->nama, "username" => $request->username]);
                if ($update) {
                    if ($request->username != $user->username) {
                        auth()->logout();
                    }
                    return $this->responseSuccess([
                        'message' => 'success update profile'
                    ]);
                } else {
                    return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'something error'], 400);
                }
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, [
                'message' => 'Internal server error'
            ], 500);
        }
    }
    public function changePassword(Request $request)
    {
        try {
            $credentials = $request->only(['password', 'new_password', 'confirm_password']);
            $validate = Validator::make($credentials, ['password' => 'required', 'new_password' => 'required', 'confirm_password' => 'required|same:new_password'], ['password.required' => 'Password harus di isi', 'confirm_password.required' => 'Konfirmasi Password harus di isi', 'confirm_password.same' => "Konfirmasi password tidak sama", "new_password.required" => 'Password baru harus di isi']);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
            } else {
                $user = User::find(auth()->user()->id);
                if (Hash::check($request->password, $user->password)) {
                    $update = User::where('id', auth()->user()->id)->update(["password" => Hash::make($request->new_password)]);
                    if ($update) {
                        auth()->logout();
                        return $this->responseSuccess([
                            'message' => 'success update password'
                        ]);
                    } else {
                        return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'something error'], 400);
                    }
                } else {
                    return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => ['password' => ['Password invalid']]], 400);
                }
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, [
                'message' => 'Internal server error'
            ], 500);
        }
    }
}
