<?php

namespace App\Models;

use App\Builder\DataTableBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    use HasFactory;
    protected $table = "mahasiswa";
    protected $fillable = ['nim', 'nama', 'id_kelas'];

    public function newEloquentBuilder($query)
    {
        return new DataTableBuilder($query);
    }

    public function Points(): HasMany
    {
        return $this->hasMany(Point::class, 'id_mahasiswa', 'id');
    }
    public function Kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }
}
