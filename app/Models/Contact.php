<?php

namespace App\Models;

use App\ContactRoleEnum;
use App\ContactStatusEnum;
use App\Http\Helpers\ContactHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;


class Contact extends Model
{
     use Notifiable;
    /*
   |--------------------------------------------------------------------------
   | VARIABLES GLOBAL
   |--------------------------------------------------------------------------
   */
    protected $table      =   'contacts';
    protected $fillable     =   ['lastname','firstname','contact_status','status','email','phone'];

    /*
     *--------------------------------------------------------------------------
     * Appends function to get access to new property 'name' for sendinblue
     * --------------------------------------------------------------------------
     */

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function getUndefinedStatus(): string
    {
        return $this->status = ContactStatusEnum::WAITING;
    }

}
