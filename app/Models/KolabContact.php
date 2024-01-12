<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KolabContact extends Model
{ 
    use HasFactory;

    protected $table = 'contact';

    //protected $guard_name = ['id'] ;

    protected $fillable = [
        'name','firstname','email','phone','instagram_url',
        'vimeo_url', 'youtube_url', 'city', 'country', 
        'status', 'job_id'
    ];

    protected $appends = ['skills', 'job'] ;

    /**
     * @return BelongsToMany
     */

    public function skills()
    {
        return $this->belongsToMany('App\Models\Skill', 'skill_contact', 'contact_id', 'skill_id');
    }
    public function getSkillsAttribute()
    {
        return $this->skills()->getResults();
    }
     /**
     * @return BelongsTo
     */
    public function job()
    {
        return $this->hasMany('App\Models\Job','id','job_id');
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
     * @return BelongsTo
     */
    
    public function workspace()
    {
        return $this->hasOne('\App\Models\Workspace', 'id');
    }

    public function getWorkspaceAttribute()
    {
        return $this->workspace()->getResults();
    }
}