<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Authority extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'authorities';

    protected $fillable = [
        'urlImg',
        'publicId',
        'position',
        'autorityName',
        'id_user',
        'id_cd',
    ];

    protected $casts = [
        'urlImg' => 'string',
        'publicId' => 'string',
        'position' => 'string',
        'autorityName' => 'string',
        'id_user' => 'string',
        'id_cd' => 'string',
    ];

    public function certificateData()
    {
        return $this->belongsTo(CertificateData::class, 'id_cd', '_id');
    }
}


