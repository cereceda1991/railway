<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class CertificateData extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'certificates_data';

    protected $fillable = [
        'career_type',
        'certificateContent',
        'id_user',
    ];

    protected $casts = [
        'career_type' => 'string',
        'certificateContent' => 'string',
        'id_user' => 'string',
    ];

    public function authorities()
    {
        return $this->belongsToMany(Authority::class, null, 'certificate_id', 'authority_id');
    }
}
