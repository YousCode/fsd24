<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExplorerDrive extends Model
{
    /*
|--------------------------------------------------------------------------
| GLOBAL VARIABLES
|--------------------------------------------------------------------------
*/

    protected $table = 'explorer_drives';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ['driveLinks'];

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
     * Return the links in the ExplorerDrive
     * @return HasMany
     */
    public function driveLinks(): HasMany
    {
        return $this->hasMany('App\Models\ExplorerDriveLink', 'id_drive');
    }


    /**
     * @return BelongsTo
     */
    public function missionProposition(): BelongsTo
    {
        return $this->belongsTo('App\Models\ExplorerMissionProposition', 'id_proposition');
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

    /**
     * @return mixed
     */
    public function getDriveLinksAttribute()
    {
        return $this->driveLinks()->select('id','id_drive','name','link','miniature')->get();
        //return $this->driveLinks()->getResults();
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
