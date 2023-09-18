<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContextErrorEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\MakulResource;
use App\Http\Resources\PaginateResource;
use App\Models\Makul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MataKuliahController extends Controller
{
    private Makul $makul;

    public function __construct()
    {
        $this->makul = new Makul();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $resource = new PaginateResource($this->makul->datatables(['makul', 'sks']));
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
            $validate = Validator::make($request->only(['makul', 'sks']), ['makul' => 'required|unique:mata_kuliah,makul|max:40', 'sks' => 'required'], ['makul.required' => "makul harus di isi", 'sks.required' => 'SKS harus di isi', 'makul.unique' => 'makul sudah tersedia', 'makul.max' => 'nama makul tidak boleh lebih dari 40 karakter']);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
            } else {
                $data = [
                    'makul' => $request->makul,
                    'sks' => $request->sks
                ];
                $create = $this->makul->create($data);
                if ($create) {
                    return $this->responseSuccess(['message' => 'success create makul']);
                } else {
                    return $this->responseError(ContextErrorEnum::FAIL,['message'=>'failed'], 400);
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
            $makul = $this->makul->find($id);
            if ($makul != null) {
                return $this->responseSuccess(new MakulResource($makul));
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'makul not found'], 404);
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
            $validate = Validator::make($request->only(['makul', 'sks']), ['makul' => 'required|max:40|unique:mata_kuliah,makul,' . $id, 'sks' => 'required|numeric'], ['makul.required' => "makul harus di isi", 'sks.required' => 'SKS harus di isi', 'makul.unique' => 'makul sudah tersedia', 'makul.max' => 'nama makul tidak boleh lebih dari 40 karakter']);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
            } else {
                $data = [
                    'makul' => $request->makul,
                    'sks' => $request->sks
                ];
                $makul = $this->makul->find($id);
                if ($makul != null) {
                    $update = $this->makul->where('id', $id)->update($data);
                    if ($update) {
                        return $this->responseSuccess(['message' => 'success update makul']);
                    } else {
                        return $this->responseError(ContextErrorEnum::FAIL,['message'=>'failed'], 400);
                    }
                } else {
                    return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'makul not found'], 404);
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
            $makul = $this->makul->find($id);
            if ($makul != null) {
                $delete = $this->makul->where('id', $id)->delete();
                if ($delete) {
                    return $this->responseSuccess(['message' => 'success delete makul']);
                } else {
                    return $this->responseError(ContextErrorEnum::FAIL,['message'=>'failed'], 400);
                }
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'makul not found'], 404);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function reset()
    {
        try {
            $reset = $this->makul->where('id', ">", 0);
            if ($reset) {
                if (count($reset->get())) {
                    $reset->delete();
                    return $this->responseSuccess(['message' => 'Berhasil reset sesi']);
                } else {
                    return $this->responseSuccess(['message' => 'Berhasil reset sesi']);
                }
            } else {
                return $this->responseError(ContextErrorEnum::FAIL,['message'=>'failed'], 400);
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
                $resource = MakulResource::collection($this->makul->where('makul', 'LIKE', '%' . $search . '%')->limit(10)->get());
            } else {
                $resource = [];
            }
            return $this->responseSuccess($resource, 200);
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
}
