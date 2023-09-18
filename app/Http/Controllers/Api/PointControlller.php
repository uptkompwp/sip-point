<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContextErrorEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\PointResource;
use App\Imports\MahasiswaImport;
use App\Models\Kuis;
use App\Models\Mahasiswa;
use App\Models\Point;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Facades\Storage;

class PointControlller extends Controller
{
    protected Kuis $kuis;
    protected Point $point;
    protected Mahasiswa $mahasiswa;
    public function __construct()
    {
        $this->kuis = new Kuis();
        $this->point = new Point();
        $this->mahasiswa = new Mahasiswa();
    }
    public function index(Request $request, $kuisId)
    {
        try {
            $checkKuis = $this->kuis->find($kuisId);
            if ($checkKuis != null) {
                if ($request->filter) {
                    $decode = json_decode(json_decode($request->filter), 1);
                    if (array_key_exists('mahasiswa', $decode)) {
                        $checkMahasiswa = $this->mahasiswa->where('nim', $decode['mahasiswa'])->first();
                        $resource = new PaginateResource($this->point->where('id_kuis', $kuisId)->where('id_mahasiswa', $checkMahasiswa->id)->with(['mahasiswa' => function ($q) {
                            return $q->with('kelas');
                        }])->with(['kuis' => function ($q) {
                            return $q->with('sesi');
                        }])->datatables(), PointResource::class);
                        return $this->responseSuccess($resource);
                    } else {
                        $resource = new PaginateResource($this->point->where('id_kuis', $kuisId)->with(['mahasiswa' => function ($q) {
                            return $q->with('kelas');
                        }])->with(['kuis' => function ($q) {
                            return $q->with('sesi');
                        }])->datatables(), PointResource::class);
                        return $this->responseSuccess($resource);
                    }
                } else {
                    $resource = new PaginateResource($this->point->where('id_kuis', $kuisId)->with(['mahasiswa' => function ($q) {
                        return $q->with('kelas');
                    }])->with(['kuis' => function ($q) {
                        return $q->with('sesi');
                    }])->datatables(), PointResource::class);
                    return $this->responseSuccess($resource);
                }
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => "kuis not found"], 404);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function store(Request $request, $kuisId)
    {
        try {
            $validate = Validator::make($request->only(['mahasiswa', 'point']), ['mahasiswa' => 'required|numeric', 'point' => 'required|numeric'], ['mahasiswa' => "Mahasiswa harus di isi", 'point.required' => "Jumlah Point harus di isi"]);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
            } else {
                $checkKuis = $this->kuis->find($kuisId);
                if ($checkKuis != null) {
                    $mahasiswaId = $request->mahasiswa;
                    $checkMahasiswa = $this->mahasiswa->find($mahasiswaId);
                    if ($checkMahasiswa != null) {
                        // check kelas mahasiswa 
                        $kuis = $this->kuis->with('sesi')->find($kuisId);
                        if ($kuis->sesi->id_kelas === $checkMahasiswa->id_kelas) {
                            // check mahasiswa sudah dapat point 
                            // $pointAlreadyExist = $this->point->where('id_kuis', $kuisId)->where('id_mahasiswa', $mahasiswaId)->first();
                            // if ($pointAlreadyExist != null) {
                            //     return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => ['mahasiswa' => ['Mahasiswa dengan NIM ' . $checkMahasiswa->nim . ' telah mendapatkan point']]], 400);
                            // } else {
                            //     $create = $this->point->create(['id_mahasiswa' => $mahasiswaId, 'id_kuis' => $kuisId, 'point' => $request->point]);
                            //     if ($create) {
                            //         return $this->responseSuccess(['message' => 'success create point']);
                            //     } else {
                            //         return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                            //     }
                            // }
                            $create = $this->point->create(['id_mahasiswa' => $mahasiswaId, 'id_kuis' => $kuisId, 'point' => $request->point]);
                            if ($create) {
                                return $this->responseSuccess(['message' => 'success create point']);
                            } else {
                                return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                            }
                        } else {
                            return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => ['mahasiswa' => ['Mahasiswa tidak ditemukan']]], 400);
                        }
                    } else {
                        return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => ['mahasiswa' => ['Mahasiswa tidak ditemukan']]], 400);
                    }
                } else {
                    return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => "kuis not found"], 404);
                }
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $validate = Validator::make($request->only(['point']), ['point' => 'required|numeric']);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
            } else {
                if ($request->point > 0) {
                    $update = $this->point->where('id', $id)->update(['point' => $request->point]);
                    if ($update) {
                        return $this->responseSuccess(['message' => 'success update point']);
                    } else {
                        return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                    }
                } else {
                    return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                }
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function destroy(string $id)
    {
        try {
            $point = $this->point->find($id);
            if ($point != null) {
                $delete = $this->point->where('id', $id)->delete();
                if ($delete) {
                    return $this->responseSuccess(['message' => 'success delete point']);
                } else {
                    return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                }
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'point not found'], 404);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function reset($kuisId)
    {
        try {
            $checkkuis = $this->kuis->find($kuisId);
            if ($checkkuis != null) {
                $reset = $this->point->where('id_kuis', $kuisId)->where('id', ">", 0);
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
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => "kuis not found"], 404);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function import(Request $request)
    {
        try {
            $rows = Excel::toArray(new MahasiswaImport, $request->file('file'));
            if (count($rows[0])) {
                return $this->responseSuccess(['data' => $rows[0]]);
            } else {
                return $this->responseSuccess(['data' => []]);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function import_save(Request $request, $kuisId)
    {
        try {
            $validate = Validator::make($request->only('data'), ['data' => 'required']);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'something error request'], 400);
            } else {
                $checkKuis = $this->kuis->with('sesi')->find($kuisId);
                if ($checkKuis != null) {
                    $success = 0;
                    $failed = 0;
                    $mahasiswa = $request->data;
                    foreach ($mahasiswa as $mhs) {
                        $mhs['nim'] = $this->parseNIM($mhs['nim']);
                        $mhsValidator = Validator::make($mhs, ['nim' => 'required|max:11|regex:/\d{2}\.\d{3}\.\d{4}/u']);
                        if (!$mhsValidator->fails()) {
                            $data_mahasiswa = $this->mahasiswa->where('nim', $mhs['nim'])->first();
                            if ($data_mahasiswa != null) {
                                if ($checkKuis->sesi->id_kelas === $data_mahasiswa->id_kelas) {
                                    $create = $this->point->create([
                                        'id_mahasiswa' => $data_mahasiswa->id,
                                        'point' => $mhs['point'],
                                        'id_kuis' => $kuisId
                                    ]);
                                    if ($create) {
                                        $success++;
                                    } else {
                                        $failed++;
                                    }
                                } else {
                                    $failed++;
                                }
                            } else {
                                $failed++;
                            }
                        } else {
                            $failed++;
                        }
                    }
                    return $this->responseSuccess(['success' => $success, 'failed' => $failed]);
                } else {
                    return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => "kuis not found"], 404);
                }
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function download_format()
    {
        try {
            $download = Storage::download('/format/point.xlsx');
            return $download;
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    protected function parseNIM($val)
    {
        $p1 = substr($val, 0, 2);
        $p2 = substr($val, 2, 3);
        $p3 = substr($val, 5, 4);
        return  implode('.', [$p1, $p2, $p3]);
    }
}
