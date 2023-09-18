<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContextErrorEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaginateResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AsistenController extends Controller
{
    protected User $user;
    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $resource = new PaginateResource($this->user->where('role', "ASISTEN")->datatables(['nama', 'username']));
            return $this->responseSuccess($resource, 200);
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->only(['nama', 'username', 'password', 'confirm_password']), ['nama' => 'required', 'username' => 'required|unique:users,username', 'password' => 'required', 'confirm_password' => 'required|same:password'], [
                'nama.required' => "Nama harus di isi",
                'username.required' => "Username harus di isi",
                'password.required' => "Password harus di isi",
                'confirm_password.required' => "Konfirmasi password harus di isi",
                'username.unique' => 'Username sudah ada',
                'confirm_password.same' => 'Konfirmasi password tidak sama'

            ]);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
            } else {
                $data = [
                    'nama' => $request->nama,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                ];

                $create = $this->user->create($data);
                if ($create) {
                    return $this->responseSuccess(['message' => 'success create asisten']);
                } else {
                    return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                }
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $user = $this->user->find($id);
            if ($user != null) {
                return $this->responseSuccess($user);
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'asisten not found'], 404);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = $this->user->find($id);
            if ($user != null) {
                $validate = Validator::make($request->only(['nama', 'username']), ['nama' => 'required', 'username' => 'required|unique:users,username,' . $id], [
                    'nama.required' => "Nama harus di isi",
                    'username.required' => "Username harus di isi",
                    'username.unique' => 'Username sudah ada',
                ]);
                if ($validate->fails()) {
                    return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
                } else {
                    if ($request->password != null) {
                        $data = [
                            'nama' => $request->nama,
                            'username' => $request->username,
                            'password' => Hash::make($request->password),
                        ];
                    } else {
                        $data = [
                            'nama' => $request->nama,
                            'username' => $request->username,
                        ];
                    }
                    $update = $this->user->where('id', $id)->update($data);
                    if ($update) {
                        return $this->responseSuccess(['message' => 'success update asisten']);
                    } else {
                        return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                    }
                }
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'asisten not found'], 404);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = $this->user->find($id);
            if ($user != null) {
                $delete = $this->user->where('id', $id)->delete();
                if ($delete) {
                    return $this->responseSuccess(['message' => 'success delete asisten']);
                } else {
                    return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                }
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'asisten not found'], 404);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function reset()
    {
        try {
            $reset = $this->user->where('role', 'ASISTEN')->where('id', ">", 0);
            if ($reset) {
                if (count($reset->get())) {
                    $reset->delete();
                    return $this->responseSuccess(['message' => 'Berhasil reset sesi']);
                } else {
                    return $this->responseSuccess(['message' => 'Berhasil reset sesi']);
                }
            } else {
                return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
}
