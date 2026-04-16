<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class AppUser extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'app_user';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_name',
        'email_address',
        'contact_email',
        'address',
        'phone',
        'user_password',
        'user_tag',
        'is_active',
        'paddock_id',
    ];

    protected $hidden = [
        'user_password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'registration_date' => 'datetime',
    ];

    /**
     * Override para autenticación con email_address
     */
    public function getAuthPassword()
    {
        return $this->user_password;
    }

    /**
     * Relaciones
     */
    public function paddock()
    {
        return $this->belongsTo(Paddock::class, 'paddock_id', 'paddock_id');
    }

    public function adverts()
    {
        return $this->hasMany(CarAdvert::class, 'seller_id', 'user_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class, 'user_id', 'user_id');
    }

    public function likes()
    {
        return $this->belongsToMany(Post::class, 'post_like', 'user_id', 'post_id')
                    ->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(AppUser::class, 'user_follow', 'follower_id', 'followed_id')
                    ->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(AppUser::class, 'user_follow', 'followed_id', 'follower_id')
                    ->withTimestamps();
    }

    public function garage()
    {
        return $this->hasMany(UserGarage::class, 'user_id', 'user_id');
    }

    public function savedSearches()
    {
        return $this->hasMany(SavedSearch::class, 'user_id', 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'user_id');
    }

    public function createdEvents()
    {
        return $this->hasMany(EventKdd::class, 'creator_id', 'user_id');
    }

    public function attendingEvents()
    {
        return $this->belongsToMany(EventKdd::class, 'event_attendance', 'user_id', 'event_id')
                    ->withTimestamps();
    }
}
