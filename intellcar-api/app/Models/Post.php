<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'post';
    protected $primaryKey = 'post_id';

    protected $fillable = [
        'author_id',
        'title',
        'content',
        'model_id',
        'engine_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relaciones
     */
    public function author()
    {
        return $this->belongsTo(AppUser::class, 'author_id', 'user_id');
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class, 'model_id', 'model_id');
    }

    public function engine()
    {
        return $this->belongsTo(CarEngine::class, 'engine_id', 'engine_id');
    }

    public function moods()
    {
        return $this->belongsToMany(Paddock::class, 'post_moods', 'post_id', 'mood_id');
    }

    public function media()
    {
        return $this->hasMany(PostMedia::class, 'post_id', 'post_id');
    }

    public function likes()
    {
        return $this->belongsToMany(AppUser::class, 'post_like', 'post_id', 'user_id')
                    ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class, 'post_id', 'post_id');
    }
}
