<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Spatie\DataTransferObject\DataTransferObject;
use App\User;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'tasks';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ['taskTypes', 'talentId', 'owner', 'talentName', 'tasks', 'viewed'];
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
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    /**
     * @return BelongsToMany
     */
    public function taskTypes()
    {
        return $this->belongsToMany('App\Models\TaskType', 'task_type_task');
    }

    /**
     * @return BelongsToMany
     */
    public function historic()
    {
        return $this->belongsToMany('App\User', 'tasks_historic', 'task_id', 'from_user_id');
    }

    /**
     * The talents that part of the project.
     */
    public function talents()
    {
        return $this->belongsToMany('App\User', 'task_talent');
    }

    public function tasks()
    {
        return $this->hasMany('App\Models\TaskDates');
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
     * @return Model|BelongsTo|object|null
     */
    public function getProjectAttribute()
    {
        return $this->project()->first();
    }

    /**
     * @return Collection|mixed
     */
    public function getTaskTypesAttribute()
    {
        return $this->taskTypes()->getResults();
    }

    /**
     * @return Collection|mixed
     */
    public function getTasksAttribute()
    {
        return $this->tasks()->getResults();
    }

    /**
     * @return Collection|mixed
     */
    public function getTalentsAttribute()
    {
        return $this->talents()->getResults();
    }

    public function getTalentIdAttribute()
    {
        return DB::table('task_talent')->where('task_id', $this->id)->get();
    }

    public function getTalentNameAttribute()
    {
        $talentId = false;
        if (!empty($this->getTalentIdAttribute()->toArray()[0]->user_id)) {
            $talentId = $this->getTalentIdAttribute()->toArray()[0]->user_id;
            $talent = User::find($talentId);
            return $talent->firstname;
        }
        return $talentId;
    }

    public function getOwnerAttribute()
    {
        $owner = DB::table('users')->where('id', $this->created_by)->first();
        if ($owner) {
            return $owner;
        }

        return false;
    }

    public function getViewedAttribute()
    {
        $viewed = [];
        $tasks = DB::table('task_viewed')->where('task_id', $this->id)->groupBy('user_id')->get();
        if ($tasks) {
            foreach($tasks as $task) {
                $viewed[$task->user_id] = $task->id;
            }

            return json_encode($viewed);
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
