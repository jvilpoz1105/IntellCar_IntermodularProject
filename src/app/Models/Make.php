<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Make extends Model
{
    use HasFactory;

    protected $table = 'make';
    protected $primaryKey = 'make_id';

    protected $fillable = [
        'make_name',
        'origin_country',
        'official_website',
        'status',
    ];

    /**
     * Relaciones
     */
    public function models()
    {
        return $this->hasMany(CarModel::class, 'make_id', 'make_id');
    }

    public function engines()
    {
        return $this->hasMany(CarEngine::class, 'make_id', 'make_id');
    }
}
