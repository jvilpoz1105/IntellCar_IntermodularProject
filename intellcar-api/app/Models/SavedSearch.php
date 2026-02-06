<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedSearch extends Model
{
    use HasFactory;

    protected $table = 'saved_search';
    protected $primaryKey = 'search_id';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'search_name',
        'filters_json',
    ];

    protected $casts = [
        'filters_json' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relaciones
     */
    public function user()
    {
        return $this->belongsTo(AppUser::class, 'user_id', 'user_id');
    }
}
