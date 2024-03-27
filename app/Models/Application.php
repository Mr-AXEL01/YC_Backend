<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable=[
        'volunteer_id',
        'announcement_id',
        'status',
    ];

    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(User::class ,'volunteer_id');
    }

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class ,'announcement_id');
    }

}
