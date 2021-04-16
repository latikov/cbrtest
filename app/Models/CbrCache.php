<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CbrCache extends Model
{
    use HasFactory;

    protected $fillable = ['day', 'rates'];

    protected $casts = [
        'rates' => 'array'
    ];
}
