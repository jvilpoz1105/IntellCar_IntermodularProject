<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdMedia extends Model
{
    use HasFactory;

    protected $table = 'ad_media';
    protected $primaryKey = 'media_id';

    protected $fillable = [
        'media_url',
        'media_type',
        'ad_id',
    ];

    /**
     * Relaciones
     */
    public function advert()
    {
        return $this->belongsTo(CarAdvert::class, 'ad_id', 'ad_id');
    }
}
