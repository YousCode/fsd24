<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExplorerRegistration extends Model
{
    protected $table = 'explorer_registrations';
    protected $fillable = [
        'status'
    ];
    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsTo('App\User');
    }
}
