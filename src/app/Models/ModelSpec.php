<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModelSpec extends Model
{
    use HasFactory;

    protected $table = 'model_specs'; 
    protected $primaryKey = 'id';

    protected $fillable = [
        'spec_name',
        'value',
        'meassurement_unit',
        'variable_type'
    ];
    
    public function models()
    {
        return $this->belongsToMany(CarModel::class, 'id', 'model_id');
    }
}

