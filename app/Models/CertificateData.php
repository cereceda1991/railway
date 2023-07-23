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
        'institution',
        'emission_date',
        'certificateTitle',
        'certificateContent',
        'career_type',
        'id_user',
    ];

    protected $casts = [
        'institution' => 'string',
        'emission_date' => 'string',
        'certificateTitle' => 'string',
        'certificateContent' => 'string',
        'career_type' => 'string',
        'id_user' => 'string',
    ];

    protected $hidden = [
        'authority_id',
    ];

    public function authorities()
    {
        return $this->belongsToMany(Authority::class, null, 'certificate_id', 'authority_id');
    }

    public function logos()
    {
        return $this->hasMany(Logo::class, 'cd_id');
    }
}
