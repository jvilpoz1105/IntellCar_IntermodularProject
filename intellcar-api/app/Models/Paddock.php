<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paddock extends Model
{
    use HasFactory;

    protected $table = 'paddock';
    protected $primaryKey = 'paddock_id';

    protected $fillable = [
        'paddock_name',
        'paddock_description',
    ];

    /**
     * Relaciones
     */
    public function users()
    {
        return $this->hasMany(AppUser::class, 'paddock_id', 'paddock_id');
    }

    public function adverts()
    {
        return $this->belongsToMany(CarAdvert::class, 'advert_moods', 'mood_id', 'ad_id');
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_moods', 'mood_id', 'post_id');
    }

    public function events()
    {
        return $this->hasMany(EventKdd::class, 'paddock_id', 'paddock_id');
    }
}
