<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'content',
    ];

    // one reply belongs to one ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // one reply belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
