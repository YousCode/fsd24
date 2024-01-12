<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    //

    /**
     * The users that belong to the role.
     */
    public function appointments()
    {
        return $this->belongsTo('App\Models\Appointment');
    }
}
