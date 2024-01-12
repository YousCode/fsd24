<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use DB;

class ExplorerMissionQuote extends Model
{
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'explorer_mission_quote';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ['proposition', 'currencySymbol'];

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

    public function proposition(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\ExplorerMissionProposition', 'id_proposition');
    }

    public function kolabTask(): BelongsTo
    {
        return $this->belongsTo('App\Models\Task', 'kolab_task_id');
    }
    public function currency()
    {
        return $this->hasMany('App\Models\Currency','id','currency_id');
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

    public function getPropositionAttribute()
    {
        return $this->proposition()->first();
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function getStripeAccIdFromUserId($userId) {
        $quotes = DB::table('explorer_mission_quote')->where('created_by', $userId)->get();
        foreach ($quotes as $quote){
            if($quote && $quote->freelance_Stripe_accId != '')
            {
                return $quote->freelance_Stripe_accId;
            }
        }
        return false;
    }

    public function getCurrencySymbolAttribute() {
        $currencySymbol = "€";
        if ($this->currency() && $this->currency()->first()) {
            $currencySymbol = $this->currency()->first()->symbol;
        }

        return $currencySymbol;
    }
}
