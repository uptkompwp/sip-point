<?php

namespace App\Models;

use App\Builder\DataTableBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kuis extends Model
{
    use HasFactory;
    protected $fillable = ['kuis', 'id_sesi', 'tipe_kuis'];
    public function Sesi(): BelongsTo
    {
        return $this->belongsTo(Sesi::class, 'id_sesi', 'id');
    }
    public function Points(): HasMany
    {
        return $this->hasMany(Point::class, 'id_kuis', 'id');
    }

    public function newEloquentBuilder($query)
    {
        return new DataTableBuilder($query);
    }
}
