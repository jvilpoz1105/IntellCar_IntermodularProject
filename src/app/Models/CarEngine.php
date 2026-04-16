<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarEngine extends Model
{
    use HasFactory;

    protected $table = 'car_engine';
    protected $primaryKey = 'engine_id';

    protected $fillable = [
        'engine_name',
        'engine_description',
        'fuel_type',
        'make_id',
    ];

    /**
     * Relaciones
     */
    public function make()
    {
        return $this->belongsTo(Make::class, 'make_id', 'make_id');
    }

    public function adverts()
    {
        return $this->hasMany(CarAdvert::class, 'engine_id', 'engine_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'engine_id', 'engine_id');
    }

    public function garageItems()
    {
        return $this->hasMany(UserGarage::class, 'motor_id', 'engine_id');
    }

    public function specs()
    {
        return $this->hasMany(EngineSpec::class, 'sp_engine', 'engine_id');
    }

}
