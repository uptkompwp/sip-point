<?php

namespace App\Exports;

use App\Models\Kelas;
use App\Models\Mahasiswa;
use App\Models\Makul;
use App\Models\Sesi;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MahasiswaExport implements FromView
{
    private Kelas $kelas;
    private Makul $makul;
    public function __construct(Kelas $kelas, Makul $makul)
    {
        $this->kelas = $kelas;
        $this->makul = $makul;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $sesi = new Sesi();
        $mhs = new Mahasiswa();
        $mahasiswa = $mhs->where('id_kelas', $this->kelas->id)->get();
        $sesi = $sesi->where('id_kelas', $this->kelas->id)->where('id_makul', $this->makul->id)->get();
        $kelas = $this->kelas;
        $makul = $this->makul;
        return view('Excel/report', compact('mahasiswa', 'sesi','kelas','makul'));
    }
}
