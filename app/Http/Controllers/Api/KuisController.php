<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContextErrorEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\KuisResource;
use App\Http\Resources\PaginateResource;
use App\Models\Kuis;
use App\Models\Sesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KuisController extends Controller
{
    protected Kuis $kuis;
    protected Sesi $sesi;
    public function __construct()
    {
        $this->kuis = new Kuis();
        $this->sesi = new Sesi();
    }


    public function index($sesiId)
    {
        try {
            $checkSesi = $this->sesi->find($sesiId);
            if ($checkSesi != null) {
                $resource = new PaginateResource($this->kuis->where('id_sesi', $sesiId)->withcount('Points')->datatables(['kuis']), KuisResource::class);
                return $this->responseSuccess($resource);
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => "sesi not found"], 404);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function store(Request $request, $sesiId)
    {
        try {
            $validate = Validator::make($request->only(['kuis', 'type']), ['kuis' => 'required', 'type' => 'required'], ['kuis.required' => "Kuis harus di isi", "type.required" => "Tipe kuis harus di isi"]);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
            } else {
                if (in_array($request->type, ['PRAKTEK', 'TEORI'])) {
                    $checkSesi = $this->sesi->find($sesiId);
                    if ($checkSesi != null) {
                        $data = [
                            'kuis' => $request->kuis,
                            'point' => $request->point,
                            'id_sesi' => $sesiId,
                            'tipe_kuis' => $request->type
                        ];
                        $create = $this->kuis->create($data);
                        if ($create) {
                            return $this->responseSuccess(['message' => 'success create kuis']);
                        } else {
                            return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                        }
                    } else {
                        return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => "sesi not found"], 404);
                    }
                } else {
                    return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => ['type' => ['Tipe kuis tidak sesuai']]], 400);
                }
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function edit($id)
    {
        try {
            $kuis = $this->kuis->find($id);
            if ($kuis != null) {
                return $this->responseSuccess(new KuisResource($kuis));
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'sesi not found'], 404);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $validate = Validator::make($request->only(['kuis', 'type']), ['kuis' => 'required', 'type' => 'required'], ['kuis.required' => "Kuis harus di isi", "type.required" => "Tipe kuis harus di isi"]);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
            } else {
                if (in_array($request->type, ['PRAKTEK', 'TEORI'])) {
                    $data = [
                        'kuis' => $request->kuis,
                        'tipe_kuis' => $request->type
                    ];
                    $update = $this->kuis->where('id', $id)->update($data);
                    if ($update) {
                        return $this->responseSuccess(['message' => 'success update kuis']);
                    } else {
                        return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                    }
                } else {
                    return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => ['type' => ['Tipe kuis tidak sesuai']]], 400);
                }
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function reset($sesiId)
    {
        try {
            $checkSesi = $this->sesi->find($sesiId);
            if ($checkSesi != null) {
                $reset = $this->kuis->where('id_sesi', $sesiId)->where('id', ">", 0);
                if ($reset) {
                    if (count($reset->get())) {
                        $reset->delete();
                        return $this->responseSuccess(['message' => 'Berhasil reset kuis']);
                    } else {
                        return $this->responseSuccess(['message' => 'Berhasil reset kuis']);
                    }
                } else {
                    return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                }
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => "sesi not found"], 404);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function destroy(string $id)
    {
        try {
            $kuis = $this->kuis->find($id);
            if ($kuis != null) {
                $delete = $this->kuis->where('id', $id)->delete();
                if ($delete) {
                    return $this->responseSuccess(['message' => 'success delete kuis']);
                } else {
                    return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                }
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'kuis not found'], 404);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function destroy_selected(Request $request)
    {
        try {
            $validate = Validator::make($request->only('checkeds'), ['checkeds' => 'required']);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
            } else {
                $checkeds = $request->checkeds;
                $delete = $this->kuis->whereIn('id', $checkeds)->delete();
                if ($delete) {
                    return $this->responseSuccess(['message' => 'success delete kuis']);
                } else {
                    return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                }
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
}
