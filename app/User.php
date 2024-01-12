<?php

namespace App;

use App\Models\Appointment;
use App\Models\Portfolio;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles, Billable;

    protected $table = "users";

    protected $guard_name = 'web';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'firstname', 'lastname', 'phone',
        'job_id', 'description', 'website','client_job','company','is_user',
        'firstlogin','email_verified_at','user_status','certified'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'created_at', 'deleted_by', 'updated_at', 'updated_by',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['skills', 'job', 'role', 'portfolios', 'lastPortfolioMainMedia', 'skillIds', 'randomNumber'];

    /**
     * @return BelongsToMany
     */
    public function skills()
    {
        return $this->belongsToMany('App\Models\Skill', 'skill_user', 'user_id', 'skill_id');
    }

    public function conversation()
    {
        return $this->hasMany('App\Models\ExplorerMissionConversationMessage','id_conversation', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany('App\Models\Project', 'project_talent', 'user_id', 'project_id');
    }

    /**
     * @return HasMany
     */
    public function explorer()
    {
        return $this->hasMany('App\Models\ExplorerRegistration');
    }
    /**
     * @return HasMany
     */
    public function notification():HasMany
    {
        return $this->hasMany('App\Models\Notification','user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function tasks()
    {
        return $this->belongsToMany('App\Models\Task', 'task_talent', 'user_id', 'task_id');
    }

     /**
     * @return BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany('App\Models\Groups', 'groups_talent', 'user_id', 'group_id');
    }
    /**
     * @return BelongsToMany
     */
    public function appointments()
    {
        return $this->belongsToMany('App\Models\Appointment', 'appointment_talent',  'user_id', 'appointment_id');
    }

    /**
     * @return BelongsToMany
     */
    public function portfolios()
    {
        return $this->belongsToMany('App\Models\Portfolio', 'portfolios_talents', 'user_id', 'portfolio_id');
    }

    /**
     * @return BelongsTo
     */
    public function workspace()
    {
        return $this->hasOne(\App\Models\Workspace::class, 'owner_id');
    }

    /**
     * @return BelongsTo
     */
    public function currentWorkspace()
    {
        return $this->hasOne(\App\Models\Workspace::class, 'current_workspace_id');
    }

    /**
     * @return BelongsToMany
     */
    public function workspaces()
    {
        return $this->belongsToMany('App\Models\Workspace', 'workspace_members', 'user_id', 'workspace_id');
    }

    /**
     * @return BelongsToMany
     */
    public function explorerCodes()
    {
        return $this->belongsToMany('App\Models\ExplorerCode', 'explorer_code_users', 'user_id', 'explorer_code_id');
    }

    /**
     * Return the skills attribute
     *
     * @return Collection|mixed
     */
    public function getSkillsAttribute()
    {
        return $this->skills()->getResults();
    }

    /**
     * Return the projects attribute
     *
     * @return Collection|mixed
     */
    public function getProjectsAttribute()
    {
        return $this->projects()->getResults();
    }

    /**
     * @return BelongsTo
     */
    public function job()
    {
        return $this->hasMany('App\Models\Job','id','job_id');
    }

    public function getExplorerRegistrationAttribute()
    {
        return $this->explorer()->getResults();
    }
    public function getNotificationAttribute(){
        return $this->notification()->getResults();
    }
    /**
     * Return the job attribute
     *
     * @return Collection|mixed
     */
    public function getJobAttribute()
    {
        return $this->job()->first();
    }
    /**
     * Return the role attribute
     *
     * @return Collection|mixed
     */

    public function getRoleAttribute()
    {
        return $this->roles()->getResults();
    }
    /**
     * Return the tasks attribute
     *
     * @return Collection|mixed
     */
    public function getTasksAttribute()
    {
        return $this->tasks()->getResults();
    }


    /**
     * Return the tasks attribute
     *
     * @return Collection|mixed
     */
    public function getPortfoliosAttribute()
    {
        return $this->portfolios()->getResults();
    }
    public function getExplorerAttribute(){
        return $this->conversation()->getResults();
    }
    public function getLastPortfolioMainMediaAttribute()
    {
        $portfolios = $this->getPortfoliosAttribute();
        if (!empty($portfolios)) {
            foreach ($portfolios as $portfolio) {
                if ($portfolio == null) {
                    continue;
                }
                $media = $portfolio->mainMedia;
                if ($media == null || $media['path'] == '/images/explorer/kolab-logo.png') {
                    continue;
                } else {
                    if($portfolio->video_url_image) {
                        $media->video_url_image = $portfolio->video_url_image;
                    }
                    return $media;
                }
            }
        }

        return null;

        /*$firstPortfolio = $this->portfolios()->first();
        if ($firstPortfolio == null) {
            return null;
        }
        $media = $firstPortfolio->mainMedia;

        if ($media == null || $media['path'] == '/images/explorer/kolab-logo.png') {
            return null;
        } else {
            return $media;
        }*/

    }
    public function getRandomNumberAttribute()
    {
        return rand(1,5);
    }

    public function getId()
    {
        return $this->id;
    }
    public function getFirstLogin(){
        return $this->firstlogin;
    }

    public function getWorkspacesAttribute()
    {
        return $this->workspaces()->getResults();
    }

    public function getCurrentWorkspaceAttribute() {
        if ($this->current_workspace_id) {
            return $this->current_workspace_id;
        } else if ($this->workspaces()->first()) {
            return $this->workspaces()->first()->id;
        }

        return 1;
    }

    public function getTaskCreatorAttribute()
    {
        if ($this->taskCreatorId) {
            $user = User::findOrFail($this->taskCreatorId);
            if ($user) {
                return $user->firstname;
            }
        }

        return false;
    }

    public function getExplorerCodesAttribute()
    {
        return $this->explorerCodes()->getResults();
    }

    public function getSkillIdsAttribute() {
        $skillIds = [];
        $skills = $this->skills()->getResults();

        foreach ($skills as $skill) {
            $skillIds[] = $skill->id;
        }

        return $skillIds;
    }

}
