<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Signature extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'signatures';

    protected $fillable = [
        'urlImg',
        'publicId',
        'position',
        'autorityName',
        'id_user'
    ];

    protected $casts = [
        'urlImg' => 'string',
        'publicId' => 'string',
        'position' => 'string',
        'autorityName' => 'string',
        'id_user' => 'string'
    ];

}

