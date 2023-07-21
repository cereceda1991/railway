<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'templates';

    protected $fillable = [
        'urlImg',
        'publicId',
        'name',
        'status'
    ];

    protected $casts = [
        'urlImg' => 'string',
        'publicId' => 'string',
        'name' => 'string',
        'status' => 'boolean'
    ];

    public function thumbnail()
    {
        return $this->hasOne(ThumbnailTemplate::class, 'template_id');
    }
}




