<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngineSpec extends Model
{
    use HasFactory;

    protected $table = 'engine_spec'; 
    protected $primaryKey = 'spec_id';

    protected $fillable = [
        'sp_key',
        'sp_value',
        'measurement_unit',
        'variable_type',
        'sp_engine'
    ];
    
    public function engine()
    {
        return $this->belongsTo(CarEngine::class, 'sp_engine', 'engine_id');
    }
}
