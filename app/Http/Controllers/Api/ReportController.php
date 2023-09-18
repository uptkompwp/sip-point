<?php

namespace App\Http\Controllers\Api;

use App\Enums\ContextErrorEnum;
use App\Exports\MahasiswaExport;
use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Makul;
use App\Models\Point;
use App\Models\Sesi;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function info()
    {
        try {
            $sesi = new Sesi();
            $kelas = new Kelas();
            $mahasiswa = new Mahasiswa();
            $makul = new Makul();

            return $this->responseSuccess([
                'sesi' => $sesi->count(),
                'kelas' => $kelas->count(),
                'mahasiswa' => $mahasiswa->count(),
                'makul' => $makul->count(),
            ]);
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        };
    }
    public function export(Request $request)
    {
        try {
            $validate = Validator::make($request->only(['makul', 'kelas']), ['makul' => 'required|numeric', 'kelas' => 'required|numeric'], ['kelas.required' => "Kelas harus di isi", 'makul.required' => "Mata kuliah harus di isi"]);
            if (!$validate->fails()) {
                $kelas = Kelas::find($request->kelas);
                $makul = Makul::find($request->makul);
                if ($kelas == null) {
                    return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'kelas not found'], 404);
                } else if ($makul == null) {
                    return $this->responseError(ContextErrorEnum::NOT_FOUND, ['message' => 'mata kuliah not found'], 404);
                } else {
                    return Excel::download(new MahasiswaExport($kelas, $makul), 'laporan.xlsx', \Maatwebsite\Excel\Excel::XLSX);
                }
            } else {
                return $this->responseError(ContextErrorEnum::VALIDATIONS, ['validations' => $validate->errors()], 400);
            }
        } catch (\Exception $e) {
            return $this->responseError(ContextErrorEnum::INTERNAL_SERVER_ERROR, ['message' => 'Internal server error'], 500);
        }
    }

    public function getDetail(string $id_mhs, string $id_sesi, $type)
    {
        $point = new Point();
        return $point->join('kuis', 'kuis.id', '=', 'points.id_kuis')->where('id_sesi', $id_sesi)->selectRaw("SUM(points.point) as total_points")->where('points.id_mahasiswa', $id_mhs)->where('kuis.tipe_kuis', $type)->groupBy('points.id_mahasiswa')->first()['total_points'] ?? 0;
    }
    public function getTotal(string $id_mhs, $type)
    {
        $point = new Point();
        return  $point->join('kuis', 'kuis.id', '=', 'points.id_kuis')->selectRaw("SUM(points.point) as total_points")->where('points.id_mahasiswa', $id_mhs)->where('kuis.tipe_kuis', $type)->groupBy('points.id_mahasiswa')->first()['total_points'] ?? 0;
    }
}
