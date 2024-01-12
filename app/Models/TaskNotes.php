<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskNotes extends Model
{
    //Global variable


    protected $table = "task_notes";
    // protected $primaryKey = 'id';
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    //protected $appends = [];


    /**
     * @return BelongsTo
     */
    public function tasks()
    {
        return $this->belongsTo('App\Models\Task');
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
     * @return Collection|mixed
     */
    public function getTaskAttribute()
    {
        return $this->tasks()->getResults();
    }

}
