<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGarage extends Model
{
    use HasFactory;

    protected $table = 'user_garage';
    protected $primaryKey = 'garage_item_id';

    protected $fillable = [
        'user_id',
        'model_id',
        'motor_id',
        'car_nickname',
        'description',
        'is_current_car',
        'photo_url',
        'verified_owner',
    ];

    protected $casts = [
        'is_current_car' => 'boolean',
        'verified_owner' => 'boolean',
    ];

    /**
     * Relaciones
     */
    public function user()
    {
        return $this->belongsTo(AppUser::class, 'user_id', 'user_id');
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class, 'model_id', 'model_id');
    }

    public function engine()
    {
        return $this->belongsTo(CarEngine::class, 'motor_id', 'engine_id');
    }
}
