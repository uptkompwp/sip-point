<?php

namespace App\Models;

use App\Builder\DataTableBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;
    protected $fillable = ['kelas'];
    public function newEloquentBuilder($query)
    {
        return new DataTableBuilder($query);
    }

    public function mahasiswa(): HasMany
    {
        return $this->hasMany(Mahasiswa::class, 'id_kelas', 'id');
    }
    public function sesi(): HasMany
    {
        return $this->hasMany(Sesi::class, 'id_kelas', 'id');
    }
}
