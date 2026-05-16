<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    use HasFactory;

    protected $table = 'post_like';
    
    // Como es una clave primaria compuesta, desactivamos el autoincremento
    public $incrementing = false;
    protected $primaryKey = ['user_id', 'post_id'];
    
    // Solo tenemos created_at en la base de datos
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'post_id',
        'created_at',
    ];
}
