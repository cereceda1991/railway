<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use MongoDB\BSON\ObjectId;

class ThumbnailTemplate extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'thumbnail_templates';

    protected $fillable = [
        'urlImg',
        'publicId',
        'template_id',
    ];

    protected $casts = [
        'urlImg' => 'string',
        'publicId' => 'string',
    ];

    // Utilizar el tipo de dato ObjectId para template_id
    protected $attributes = [
        'template_id' => null,
    ];

    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id', '_id');
    }
}

