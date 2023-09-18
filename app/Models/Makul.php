<?php

namespace App\Models;

use App\Builder\DataTableBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Makul extends Model
{
    use HasFactory;
    protected $table  = 'mata_kuliah';
    protected $fillable = ['makul', 'sks'];

    public function newEloquentBuilder($query)
    {
        return new DataTableBuilder($query);
    }
}
