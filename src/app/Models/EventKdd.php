<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventKdd extends Model
{
    use HasFactory;

    protected $table = 'event_kdd';
    protected $primaryKey = 'event_id';

    protected $fillable = [
        'creator_id',
        'paddock_id',
        'title',
        'event_description',
        'event_date',
        'location_name',
        'address',
        'city',
        'latitude',
        'longitude',
        'max_participants',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Relaciones
     */
    public function creator()
    {
        return $this->belongsTo(AppUser::class, 'creator_id', 'user_id');
    }

    public function paddock()
    {
        return $this->belongsTo(Paddock::class, 'paddock_id', 'paddock_id');
    }

    public function attendees()
    {
        return $this->belongsToMany(AppUser::class, 'event_attendance', 'event_id', 'user_id')
                    ->withPivot('joined_at');
    }
}
