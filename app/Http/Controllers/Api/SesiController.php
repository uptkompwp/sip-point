<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContextErrorEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\SesiResource;
use App\Models\Kelas;
use App\Models\Makul;
use App\Models\Sesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SesiController extends Controller
{
    private Sesi $sesi;
    private Kelas $kelas;
    private Makul $makul;

    public function __construct()
    {
        $this->sesi = new Sesi();
        $this->kelas = new Kelas();
        $this->makul = new Makul();
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->filter) {
                $decode = json_decode(json_decode($request->filter), 1);
                if (array_key_exists('kelas', $decode)) {
                    $checkKelasExist = $this->kelas->where('kelas', $decode['kelas'])->first();
                    if ($checkKelasExist != null) {
                        $id_kelas = $checkKelasExist->id;
                        $resource = new PaginateResource($this->sesi->join('kelas', 'kelas.id', '=', 'sesi_pertemuan.id_kelas')->join('mata_kuliah', 'mata_kuliah.id', '=', 'sesi_pertemuan.id_makul')->select('sesi_pertemuan.id', 'sesi_pertemuan.tanggal', 'sesi_pertemuan.pertemuan', 'sesi_pertemuan.tambahan', 'kelas.kelas as kelas', 'mata_kuliah.makul as makul')->where('id_kelas', $id_kelas)->withCount('kuis')->datatables(['pertemuan', 'tanggal']));
                        return $this->responseSuccess($resource, 200);
                    } else {
                        $resource = new PaginateResource($this->sesi->join('kelas', 'kelas.id', '=', 'sesi_pertemuan.id_kelas')->join('mata_kuliah', 'mata_kuliah.id', '=', 'sesi_pertemuan.id_makul')->select('sesi_pertemuan.id', 'sesi_pertemuan.tanggal', 'sesi_pertemuan.pertemuan', 'sesi_pertemuan.tambahan', 'kelas.kelas as kelas', 'mata_kuliah.makul as makul')->withCount('kuis')->datatables(['pertemuan', 'tanggal']));
                        return $this->responseSuccess($resource, 200);
                    }
                } else {
                    $resource = new PaginateResource($this->sesi->join('kelas', 'kelas.id', '=', 'sesi_pertemuan.id_kelas')->join('mata_kuliah', 'mata_kuliah.id', '=', 'sesi_pertemuan.id_makul')->select('sesi_pertemuan.id', 'sesi_pertemuan.tanggal', 'sesi_pertemuan.pertemuan', 'sesi_pertemuan.tambahan', 'kelas.kelas as kelas', 'mata_kuliah.makul as makul')->withCount('kuis')->datatables(['pertemuan', 'tanggal']));
                    return $this->responseSuccess($resource, 200);
                }
            } else {
                $resource = new PaginateResource($this->sesi->join('kelas', 'kelas.id', '=', 'sesi_pertemuan.id_kelas')->join('mata_kuliah', 'mata_kuliah.id', '=', 'sesi_pertemuan.id_makul')->select('sesi_pertemuan.id', 'sesi_pertemuan.tanggal', 'sesi_pertemuan.pertemuan', 'sesi_pertemuan.tambahan', 'kelas.kelas as kelas', 'mata_kuliah.makul as makul')->withCount('kuis')->datatables(['pertemuan', 'tanggal']));
                return $this->responseSuccess($resource, 200);
            }
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
            $validate = Validator::make($request->only(['pertemuan', 'kelas', 'makul', 'tanggal']), ['pertemuan' => 'required|numeric', 'kelas' => 'required', 'makul' => 'required', 'tanggal' => 'required|date_format:Y-m-d'], ['pertemuan.required' => "pertemuan harus di isi", 'kelas.required' => 'Kelas harus di isi', 'makul.required' => 'Makul harus di isi', 'tanggal.required' => 'tanggal harus di isi', 'tanggal.date_format' => 'tanggal tidak sesuai tanggal format']);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
            } else {
                // check kelas is exist 
                if ($this->kelas->find($request->kelas) === null) {
                    return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => ['kelas' => ['kelas tidak di temukan']]], 400);
                } elseif ($this->makul->find($request->makul) === null) {
                    return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => ['makul' => ['makul tidak di temukan']]], 400);
                } else {
                    $data = [
                        'pertemuan' => $request->pertemuan,
                        'id_kelas' => $request->kelas,
                        'id_makul' => $request->makul,
                        'tanggal' => $request->tanggal,
                        'tambahan' => $request->tambahan ?? false,
                    ];

                    $create = $this->sesi->create($data);
                    if ($create) {
                        return $this->responseSuccess(['message' => 'success create sesi']);
                    } else {
                        return $this->responseError(ContextErrorEnum::FAIL,['message'=>'failed'], 400);
                    }
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
            $sesi = $this->sesi->join('kelas', 'kelas.id', '=', 'sesi_pertemuan.id_kelas')->join('mata_kuliah', 'mata_kuliah.id', '=', 'sesi_pertemuan.id_makul')->select('sesi_pertemuan.id', 'sesi_pertemuan.tanggal', 'sesi_pertemuan.pertemuan', 'sesi_pertemuan.tambahan', 'kelas.kelas as kelas', 'mata_kuliah.makul as makul', 'sesi_pertemuan.id_kelas', 'sesi_pertemuan.id_makul')->find($id);
            if ($sesi != null) {
                return $this->responseSuccess(new SesiResource($sesi));
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'sesi not found'], 404);
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
            $validate = Validator::make($request->only(['pertemuan', 'kelas', 'makul', 'tanggal']), ['pertemuan' => 'required|numeric', 'kelas' => 'required', 'makul' => 'required', 'tanggal' => 'required|date_format:Y-m-d'], ['pertemuan.required' => "pertemuan harus di isi", 'kelas.required' => 'Kelas harus di isi', 'makul.required' => 'Makul harus di isi', 'tanggal.required' => 'tanggal harus di isi', 'tanggal.date_format' => 'tanggal tidak sesuai tanggal format']);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
            } else {
                // check kelas is exist 
                if ($this->kelas->find($request->kelas) === null) {
                    return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => ['kelas' => ['kelas tidak di temukan']]], 400);
                } elseif ($this->makul->find($request->makul) === null) {
                    return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => ['makul' => ['makul tidak di temukan']]], 400);
                } else {
                    $data = [
                        'pertemuan' => $request->pertemuan,
                        'id_kelas' => $request->kelas,
                        'id_makul' => $request->makul,
                        'tanggal' => $request->tanggal,
                        'tambahan' => $request->tambahan ?? false,
                    ];

                    $update = $this->sesi->where('id', $id)->update($data);
                    if ($update) {
                        return $this->responseSuccess(['message' => 'success create sesi']);
                    } else {
                        return $this->responseError(ContextErrorEnum::FAIL,['message'=>'failed'], 400);
                    }
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
            $sesi = $this->sesi->find($id);
            if ($sesi != null) {
                $delete = $this->sesi->where('id', $id)->delete();
                if ($delete) {
                    return $this->responseSuccess(['message' => 'success delete sesi']);
                } else {
                    return $this->responseError(ContextErrorEnum::FAIL,['message'=>'failed'], 400);
                }
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'sesi not found'], 404);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function reset()
    {
        try {
            $reset = $this->sesi->where('id', ">", 0);
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
    public function destroy_selected(Request $request)
    {
        try {
            $validate = Validator::make($request->only('checkeds'), ['checkeds' => 'required']);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
            } else {
                $checkeds = $request->checkeds;
                $delete = $this->sesi->whereIn('id', $checkeds)->delete();
                if ($delete) {
                    return $this->responseSuccess(['message' => 'success delete sesi']);
                } else {
                    return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                }
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
}
