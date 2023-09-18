<?php

namespace App\Models;

use App\Builder\DataTableBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sesi extends Model
{
    use HasFactory;
    protected $table = "sesi_pertemuan";
    protected $fillable = ['pertemuan', 'id_kelas', 'id_makul', 'tanggal', 'tambahan'];

    public function Kuis(): HasMany
    {
        return $this->hasMany(Kuis::class, 'id_sesi', 'id');
    }


    public function newEloquentBuilder($query)
    {
        return new DataTableBuilder($query);
    }
}
