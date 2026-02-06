<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngineSpec extends Model
{
    use HasFactory;

    protected $table = 'engine_specs'; 
    protected $primaryKey = 'id';

    protected $fillable = [
        'spec_name',
        'value',
        'meassurement_unit',
        'variable_type'
    ];
    
    public function engines()
    {
        return $this->belongsToMany(CarEngine::class, 'id', 'engine_id');
    }
}
