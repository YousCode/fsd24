<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class ExplorerMissionProposition extends Model
{

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'explorer_mission_propositions';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ['freelance', 'client', 'mission', 'drive','conversationId'];

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

    public function client(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    public function freelance(): BelongsTo
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Return the related explorer Mission
     * @return BelongsTo
     */
    public function explorerMission(): BelongsTo
    {
        return $this->belongsTo('App\Models\ExplorerMission', 'id_mission');
    }

    /**
     * @return HasOne
     */
    public function conversation(): HasOne
    {
        return $this->hasOne('App\Models\ExplorerMissionConversation', 'id_proposition');
    }

    /**
     * @return HasOne
     */
    public function drive(): HasOne
    {
        return $this->hasOne('App\Models\ExplorerDrive', "id_proposition");
    }

    public function kolabProject(): BelongsTo
    {
        return $this->belongsTo('App\Models\Project', 'kolab_project_id');
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

    public function getClientAttribute()
    {
        return  DB::table('users')->select('id','name','user_status','avatar','lastname','firstname','phone','email')->where('users.id','=',$this->client_id)->first();
        //return $this->client()->getResults();
        //return $this->client()->getResults();

    }

    public function getFreelanceAttribute()
    {
        return DB::table('users')->select('id','name','user_status','avatar','lastname','firstname','phone','email')->where('users.id','=',$this->freelance_id)->first();
        //return $this->freelance()->getResults();
    }

    public function getMissionAttribute()
    {
        return $this->explorerMission()->select('id','name','budget','deadline','description','created_by','type')->first();
        //return $this->explorerMission()->getResults();
    }

    public function getDriveAttribute()
    {
        return $this->drive()->select('id','id_proposition')->first();
        //return $this->drive()->getResults();
    }

    public function getConversationIdAttribute()
    {
        $conversationId = false;
        $conversation = DB::table('explorer_mission_conversations')->where('id_proposition', $this->id)->first();
        if ($conversation) {
            $conversationId = $conversation->id;
        }

        return $conversationId;
    }

    public function getQuoteIdAttribute()
    {
        $quoteId = false;
        $quote = DB::table('explorer_mission_quote')->where('id_proposition', $this->id)->first();
        if ($quote) {
            $quoteId = $quote->id;
        }

        return $quoteId;
    }

    /*public function getKolabProjectAttribute()
    {
        return $this->kolabProject()->first();
    }*/

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
