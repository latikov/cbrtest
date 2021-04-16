<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Preset extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'codes', 'comment'];

    protected $casts = [
        'codes' => 'array'
    ];

    public function toArray()
    {
        return [
            'key' => $this->key,
            'comment' => $this->comment,
            'codes' => $this->codes
        ];
    }


}
