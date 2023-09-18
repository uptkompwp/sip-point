<?php

namespace App\Imports;

use App\Models\Mahasiswa;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MahasiswaImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        dd($collection);
        // return new Mahasiswa([
        //     'nim' => $collection[0],
        //     'nama' => $collection[1],
        //     'kelas' => $collection[3]
        // ]);
    }
    public function rules($rows): array
    {
        dd('rules');
        return [
            'nim' => Rule::max($rows['nim'], 1),
        ];
    }
}
