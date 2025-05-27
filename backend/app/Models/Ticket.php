<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'title',
        'description',
        'status',
        'priority',
        'created_at',
        'updated_at',
        'completed_at'
    ];

    // one ticket belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // one ticket can have many replies
    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }
}
