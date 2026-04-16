<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModelSpec extends Model
{
    use HasFactory;

    protected $table = 'model_spec'; 
    protected $primaryKey = 'spec_id';

    protected $fillable = [
        'sp_key',
        'sp_value',
        'measurement_unit',
        'variable_type',
        'sp_model'
    ];
    
    public function model()
    {
        return $this->belongsTo(CarModel::class, 'sp_model', 'model_id');
    }
}

