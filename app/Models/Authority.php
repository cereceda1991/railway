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
        'authorityName',
        'id_user',
        'id_cd',
        'status', 
    ];

    protected $casts = [
        'urlImg' => 'string',
        'publicId' => 'string',
        'position' => 'string',
        'authorityName' => 'string',
        'id_user' => 'string',
        'id_cd' => 'string',
        'status' => 'boolean', 
    ];

    protected $hidden = [
        'publicId',
    ];

    public function certificateData()
    {
        return $this->belongsTo(CertificateData::class, 'id_cd', '_id');
    }
}


