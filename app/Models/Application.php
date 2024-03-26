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
    ];

    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(User::class ,'volunteer_id');
    }

}
