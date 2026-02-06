<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarAdvert extends Model
{
    use HasFactory;

    protected $table = 'car_advert';
    protected $primaryKey = 'ad_id';

    protected $fillable = [
        'ad_title',
        'ad_type',
        'ad_details',
        'price',
        'kilometers',
        'car_color',
        'year_manufacture',
        'region',
        'city',
        'visible',
        'model_id',
        'engine_id',
        'seller_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'visible' => 'boolean',
        'publish_date' => 'datetime',
    ];

    /**
     * Relaciones
     */
    public function model()
    {
        return $this->belongsTo(CarModel::class, 'model_id', 'model_id');
    }

    public function engine()
    {
        return $this->belongsTo(CarEngine::class, 'engine_id', 'engine_id');
    }

    public function seller()
    {
        return $this->belongsTo(AppUser::class, 'seller_id', 'user_id');
    }

    public function moods()
    {
        return $this->belongsToMany(Paddock::class, 'advert_moods', 'ad_id', 'mood_id');
    }

    public function media()
    {
        return $this->hasMany(AdMedia::class, 'ad_id', 'ad_id');
    }
}
