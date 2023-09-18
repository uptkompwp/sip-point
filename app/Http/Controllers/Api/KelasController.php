<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContextErrorEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\KelasResource;
use App\Http\Resources\PaginateResource;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KelasController extends Controller
{
    private Kelas $kelas;

    public function __construct()
    {
        $this->kelas = new Kelas();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $resource = new PaginateResource($this->kelas->withCount('sesi')->datatables(['kelas']));
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
            $validate = Validator::make($request->only('kelas'), ['kelas' => 'required|unique:kelas,kelas|max:20'], ['kelas.required' => "Kelas harus di isi", 'kelas.unique' => 'kelas sudah tersedia']);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
            } else {
                $data = [
                    'kelas' => $request->kelas
                ];

                $create = $this->kelas->create($data);
                if ($create) {
                    return $this->responseSuccess(['message' => 'success create kelas']);
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
            $kelas = $this->kelas->find($id);
            if ($kelas != null) {
                return $this->responseSuccess(new KelasResource($kelas));
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'kelas not found'], 404);
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
            $validate = Validator::make($request->only('kelas'),  ['kelas' => 'required|max:20|unique:kelas,kelas,' . $id], ['kelas.required' => "Kelas harus di isi", 'kelas.unique' => 'kelas sudah tersedia']);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
            } else {
                $data = [
                    'kelas' => $request->kelas
                ];
                $kelas = $this->kelas->find($id);
                if ($kelas != null) {
                    $update = $this->kelas->where('id', $id)->update($data);
                    if ($update) {
                        return $this->responseSuccess(['message' => 'success update kelas']);
                    } else {
                        return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                    }
                } else {
                    return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'kelas not found'], 404);
                }
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
            $kelas = $this->kelas->find($id);
            if ($kelas != null) {
                $delete = $this->kelas->where('id', $id)->delete();
                if ($delete) {
                    return $this->responseSuccess(['message' => 'success delete kelas']);
                } else {
                    return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                }
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'kelas not found'], 404);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function all(Request $request)
    {
        try {
            $search = $request->search;
            if (strlen($search)) {
                $resource = KelasResource::collection($this->kelas->where('kelas', 'LIKE', '%' . $search . '%')->limit(10)->get());
            } else {
                $resource = [];
            }
            return $this->responseSuccess($resource, 200);
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function reset()
    {
        try {
            $reset = $this->kelas->where('id', ">", 0);
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
