<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Appointment extends Model
{
    protected $table = 'appointments';

    protected $appends = ['unviewedUser'];

    /**
     * Return the user to which this appointment belongs.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    /**
     * @return BelongsToMany
     */
    public function talents()
    {
        return $this->belongsToMany(User::class, 'appointment_talent', 'appointment_id', 'user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function contacts()
    {
        return $this->belongsToMany(KolabContact::class, 'appointment_contact', 'appointment_id', 'contact_id');
    }

    /**
     * @return bool
     */
    public function isAnInvitedTalent($user)
    {
        if(in_array($user, $this->talents()->toArray())) {
            return true;
        }
        
        return false;
    }

    public function getUnviewedUserAttribute()
    {
        $unviewedUser = [];
        $talents = $this->talents()->select('id')->where('viewed', 0)->get();
        foreach ($talents as $talent) {
            $unviewedUser[] = $talent->id;
        }

        return $unviewedUser;
    }
}
