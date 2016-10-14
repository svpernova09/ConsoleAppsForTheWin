<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meetup extends Model
{
    protected $fillable = [
        'meetup_id',
        'name',
        'time',
        'event_url',
        'description',
        'created',
        'venue_name',
        'venue_address_1',
    ];
}
