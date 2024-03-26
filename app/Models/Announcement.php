<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable=[
        'title',
        'type',
        'date',
        'description',
        'location',
        'required_skills',
        'organizer_id',

    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class ,'organizer_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

}
