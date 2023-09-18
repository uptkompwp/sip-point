<?php

namespace App\Models;

use App\Builder\DataTableBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Point extends Model
{
    use HasFactory;
    protected $fillable = ['id_mahasiswa', 'id_kuis', 'point'];
    public function Kuis(): BelongsTo
    {
        return $this->belongsTo(Kuis::class, 'id_kuis', 'id');
    }
    public function Mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id');
    }
    public function newEloquentBuilder($query)
    {
        return new DataTableBuilder($query);
    }
}
