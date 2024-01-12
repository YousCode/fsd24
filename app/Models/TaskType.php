<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskType extends Model
{
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'task_types';
    // protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = ['id'];
    private $taskTypeIdsExplorer = [
        2, //editing
        5, //motion
        7, //VFX
        11 //color-grading
    ];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    //protected $appends = [];

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
     * @return HasMany
     */
    public function tasks()
    {
        return $this->hasMany('App\Models\Task', 'task_type_id', 'id');
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function getExplorerTaskType()
    {
        $taskType = TaskType::whereIn('id', $this->taskTypeIdsExplorer)->get();
        if ($taskType) {
            return $taskType;
        }

        return false;
    }
}
