<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExplorerMissionConversationMessage extends Model
{

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'explorer_mission_conversation_messages';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ['createdByUsername','createdByAvatar', 'quote', 'driveLink', 'missionPropositionStatus', 'files', 'contactEmail'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Return the conversation the message belongs to
     * @return BelongsTo
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo('App\Models\ExplorerMissionConversation', 'id_conversation', 'id');
    }

    /**
     * Return the quote referenced in the message if is there one.
     * @return BelongsTo
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo('App\Models\ExplorerMissionQuote', 'id_quote');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo('App\User', 'created_by', "id");
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo('App\Models\Contact', 'contact_id', "id");
    }

    public function driveLink(): BelongsTo
    {
        return $this->belongsTo('App\Models\ExplorerDriveLink', 'id_drive_link', "id");
    }

    public function files()
    {
        return $this->hasMany('App\Models\ProjectFile');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getCreatedByUsernameAttribute()
    {
        if ($this->createdByUser()->first() !== null) {
            return $this->createdByUser()->first()->name;
        }

        return '';
    }

    public function getCreatedByAvatarAttribute()
    {
        if ($this->createdByUser()->first() !== null) {
            return $this->createdByUser()->first()->avatar;
        }

        return '';
    }

    public function getQuoteAttribute()
    {
        return $this->quote()->first();
    }

    public function getDriveLinkAttribute()
    {
        return $this->driveLink()->first();
    }

    public function getFilesAttribute()
    {
        return $this->files()->getResults();
    }

    public function getMissionPropositionStatusAttribute()
    {
        if($this->conversation()->first())
        {
            return $this->conversation()->first()->proposition()->first()->status;
        }
    }

    public function getClientAttribute()
    {
        if($this->conversation()->first())
        {
            return $this->conversation()->first()->proposition()->first()->client_id;
        }
    }
    public function getFreelanceAttribute()
    {
        if($this->conversation()->first())
        {
            return $this->conversation()->first()->proposition()->first()->freelance_id;
        }
    }

    public function getContactEmailAttribute()
    {
        if ($this->contact() && $this->contact()->exists()) {
            $contact = $this->contact()->first();
            return $contact->email;
        }

        return '';
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
