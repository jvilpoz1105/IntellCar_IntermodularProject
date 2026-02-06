<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostMedia extends Model
{
    use HasFactory;

    protected $table = 'post_media';
    protected $primaryKey = 'media_id';

    protected $fillable = [
        'post_id',
        'media_url',
        'media_type',
    ];

    /**
     * Relaciones
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'post_id');
    }
}
