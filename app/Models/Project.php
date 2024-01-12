<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\ProjectHelper;
use App\Http\Helpers\PlanningHelper;
use App\ProjectStep;
use App\Models\Task;
use DateTime;
use Carbon\Carbon;
use DB;

class Project extends Model
{
    /**
     * Helpers
     * @var [Array]
     */
    private $helpers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->helpers['project'] = new ProjectHelper;
        $this->helpers['planning'] = new PlanningHelper;
    }

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'projects';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = ['created_by'];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ['client', 'category','talents', 'task', 'proposition', 'share_link', 'project_step', 'url', 'url_exports', 'url_exports_uri', 'creator', 'creatorAvatar', 'IsYourProjectExplorer', 'tasksInProgress', 'planningMonthes', 'activeTaskType', 'categoryName'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getTalents()
    {
        return $this->helpers['project']->getTalentsFromTasks($this);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\ProjectCategory');
    }

    public function talents()
    {
        return $this->belongsToMany('App\User', 'project_talent');
    }

    public function files()
    {
        return $this->hasMany('App\Models\ProjectFile');
    }

    public function task()
    {
        return $this->hasMany('App\Models\Task');
    }

    public function proposition()
    {
        return $this->hasMany('App\Models\ExplorerMissionProposition', 'kolab_project_id');
    }

    public function getIsYourProjectExplorerAttribute()
    {
        $proposition = $this->proposition()->first();
        if ($proposition) {
                $user = \Auth::user();
              if ($user) {
            if ($user->id == $proposition->freelance_id || $user->id == $proposition->client_id) {
                    return true;
                }
            }
        }

       return false;
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
        return $this->client()->first();
    }

    public function getCategoryAttribute()
    {
        return $this->category()->first();
    }

    public function getTalentsAttribute()
    {
        return $this->talents()->getResults();
    }

    public function getTaskAttribute()
    {
        return $this->task()->getResults();
    }

    public function getTasksInProgressAttribute()
    {
        $now = Carbon::now();
        $tasks = $this->task()->where('status', 'PROGRESS')->orderBy('start_date')->get();
        if ($tasks) {
            return $tasks;
        }
        return $this->task()->getResults();
    }

    public function getPropositionAttribute()
    {
        return $this->proposition()->first();
    }

    public function getShareLinkAttribute()
    {
        return $this->helpers['project']->getShareLink($this);
    }

    public function getProjectStepAttribute()
    {
        $tasks = $this->getTaskAttribute()->toArray();
        return $this->helpers['project']->getProjectStep($tasks, $this->id);
    }

    public function getUrlAttribute()
    {
        return route('app_project_detail', $this->id);
    }

    public function getUrlExportsAttribute()
    {
        return route('app_project_detail_exports', $this->id);
    }

    public function getUrlExportsUriAttribute()
    {
        $route = route('app_project_detail_exports', $this->id);
        $route = parse_url($route);

        return $route['path'] ?? '';
    }

    public function getCreatorAttribute() 
    {
        $user = $this->creator()->getResults();
        if (!empty($user)) {
            return $user->firstname . ' ' . $user->lastname;
        }

        return false;
    }

    public function getCreatorAvatarAttribute() 
    {
        $user = $this->creator()->getResults();
        if (!empty($user)) {
            return $user->avatar;
        }

        return 'user.jpg';
    }

    public function getPlanningMonthesAttribute()
    {
        $planningMonthes = $this->helpers['planning']->getPlanningMonthes($this->id, $this->date_deadline);

        return $planningMonthes;
    }

    public function getActiveTaskTypeAttribute() {
        $activeTaskType = $this->helpers['project']->getActiveTaskType($this->id);

        return $activeTaskType;
    }

    public function getCategoryNameAttribute() {
        $category = $this->category()->first();
        if ($category) {
            return $category->name;
        }

        return 'undefined';
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

}
