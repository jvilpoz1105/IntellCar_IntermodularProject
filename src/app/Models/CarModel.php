<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CarModel extends Model
{
    use HasFactory;

    protected $table = 'car_model';
    protected $primaryKey = 'model_id';

    protected $fillable = [
        'model_name',
        'make_id',
        'model_description',
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
        return $this->hasMany(CarAdvert::class, 'model_id', 'model_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'model_id', 'model_id');
    }

    public function garageItems()
    {
        return $this->hasMany(UserGarage::class, 'model_id', 'model_id');
    }
     public function specs()
    {
        return $this->hasMany(ModelSpec::class, 'sp_model', 'model_id');
    }
}

