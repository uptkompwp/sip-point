<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContextErrorEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\MahasiswaResource;
use App\Http\Resources\PaginateResource;
use App\Imports\MahasiswaImport;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Excel;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
    private Mahasiswa $mahasiswa;

    public function __construct()
    {
        $this->mahasiswa = new Mahasiswa();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $resource = new PaginateResource($this->mahasiswa->join('kelas', 'kelas.id', '=', 'mahasiswa.id_kelas')->select("mahasiswa.id", "mahasiswa.id_kelas", "mahasiswa.nama", "mahasiswa.nim", "kelas.kelas")->datatables(['nim', 'nama']), MahasiswaResource::class);
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
            $validate = Validator::make($request->only(['nim', 'nama', 'kelas']), ['nim' => 'required|max:11|regex:/\d{2}\.\d{3}\.\d{4}/u|unique:mahasiswa,nim', 'nama' => 'required', 'kelas' => 'required|numeric'], ['kelas.required' => "Kelas harus di isi", 'nama.required' => "Nama harus di isi", 'kelas.required' => "Kelas harus di isi", 'nim.required' => 'NIM harus di isi', 'nim.regex' => 'NIM harus sesuai format (xx.xxx.xxxx)', 'nim' => "Mahasiswa dengan NIM " . $request->nim . " ini sudah tersedia", 'nim.max' => 'Nim tidak boleh lebih dari 11 karakter']);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
            } else {
                $data = [
                    'nim' => $request->nim,
                    'nama' => $request->nama,
                    'id_kelas' => $request->kelas
                ];
                $kelas = Kelas::find($request->kelas);

                if ($kelas != null) {
                    $create = $this->mahasiswa->create($data);
                    if ($create) {
                        return $this->responseSuccess(['message' => 'success add mahasiswa']);
                    } else {
                        return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                    }
                } else {
                    return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => ['kelas' => ['kelas tidak di temukan']]], 400);
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
            $mahasiswa = $this->mahasiswa->join('kelas', 'kelas.id', '=', 'mahasiswa.id_kelas')->find($id);
            if ($mahasiswa != null) {
                return $this->responseSuccess(new MahasiswaResource($mahasiswa));
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'mahasiswa not found'], 404);
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
            $validate = Validator::make($request->only(['nim', 'nama', 'kelas']), ['nim' => 'required|max:11|regex:/\d{2}\.\d{3}\.\d{4}/u|unique:mahasiswa,nim,' . $id, 'nama' => 'required', 'kelas' => 'required|numeric'], ['kelas.required' => "Kelas harus di isi", 'nama.required' => "Nama harus di isi", 'kelas.required' => "Kelas harus di isi", 'nim.required' => 'NIM harus di isi', 'nim.regex' => 'NIM harus sesuai format (xx.xxx.xxxx)', 'nim' => "Mahasiswa dengan NIM " . $request->nim . " ini sudah tersedia", 'nim.max' => 'Nim tidak boleh lebih dari 11 karakter']);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
            } else {
                $data = [
                    'nim' => $request->nim,
                    'nama' => $request->nama,
                    'id_kelas' => $request->kelas
                ];
                $kelas = Kelas::find($request->kelas);

                if ($kelas != null) {
                    $update = $this->mahasiswa->where('id', $id)->update($data);
                    if ($update) {
                        return $this->responseSuccess(['message' => 'success update mahasiswa']);
                    } else {
                        return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                    }
                } else {
                    return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => ['kelas' => ['kelas tidak di temukan']]], 400);
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
            $mahasiswa = $this->mahasiswa->find($id);
            if ($mahasiswa != null) {
                $delete = $this->mahasiswa->where('id', $id)->delete();
                if ($delete) {
                    return $this->responseSuccess(['message' => 'success delete mahasiswa']);
                } else {
                    return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                }
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'mahasiswa not found'], 404);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }

    public function reset()
    {
        try {
            $reset = $this->mahasiswa->where('id', ">", 0);
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
    public function import_save(Request $request)
    {
        try {
            $validate = Validator::make($request->only('data'), ['data' => 'required']);
            if ($validate->fails()) {
                return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'something error request'], 400);
            } else {
                $success = 0;
                $failed = 0;
                $mahasiswa = $request->data;
                foreach ($mahasiswa as $mhs) {
                    if ($this->getkelasId($mhs['kelas'])) {
                        $mhs['nim'] = $this->parseNIM($mhs['nim']);
                        $mhs['kelas'] = $this->getkelasId($mhs['kelas'])->id;
                        $mhsValidator = Validator::make($mhs, ['nim' => 'required|max:11|regex:/\d{2}\.\d{3}\.\d{4}/u|unique:mahasiswa,nim', 'nama' => 'required', 'kelas' => 'required|numeric']);
                        if (!$mhsValidator->fails()) {
                            $create = $this->mahasiswa->create([
                                'nim' => $mhs['nim'],
                                'nama' => $mhs['nama'],
                                'id_kelas' => $mhs['kelas'],
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
                }
                return $this->responseSuccess(['success' => $success, 'failed' => $failed]);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }

    public function download_format()
    {
        try {
            $download = Storage::download('/format/mahasiswa.xlsx');
            return $download;
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
                $delete = $this->mahasiswa->whereIn('id', $checkeds)->delete();
                if ($delete) {
                    return $this->responseSuccess(['message' => 'success delete mahasiswa']);
                } else {
                    return $this->responseError(ContextErrorEnum::FAIL, ['message' => 'failed'], 400);
                }
            }
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
    protected function getkelasId($val)
    {
        $kelas = new Kelas();
        return $kelas->where('kelas', $val)->first();
    }
    public function all(Request $request)
    {
        try {
            $search = $request->search;
            if (strlen($search)) {
                $resource = MahasiswaResource::collection($this->mahasiswa->where('nim', 'LIKE', '%' . $search . '%')->limit(10)->get());
            } else {
                $resource = [];
            }
            return $this->responseSuccess($resource, 200);
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
    public function history(string $id)
    {
        try {
            $mahasiswa = $this->mahasiswa->find($id);
            if ($mahasiswa != null) {
                $resource = new PaginateResource($this->mahasiswa->where('mahasiswa.id', $id)->join('points', 'points.id_mahasiswa', '=', 'mahasiswa.id')->join('kuis', 'points.id_kuis', '=', 'kuis.id')->join('sesi_pertemuan', 'kuis.id_sesi', '=', 'sesi_pertemuan.id')->orderBy('sesi_pertemuan.created_at', 'DESC')->datatables(['nim', 'nama']));
                return $this->responseSuccess($resource);
            } else {
                return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'mahasiswa not found'], 404);
            }
        } catch (\Exception $e) {
            dd($e);
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }
}
