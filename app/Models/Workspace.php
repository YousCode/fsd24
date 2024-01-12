<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Workspace
 * @attr integer $id
 * @attr string $name
 * @attr integer $owner_id
 * @attr App\Models\User $owner
 * @attr Carbon $created_at
 * @attr Carbon $updated_at
 * @attr Carbon $deleted_at
 */
class Workspace extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'owner_id'
    ];

    protected $with = [
        'owner',
        'members'
    ];

    protected $appends = [
        'label'
    ];

    public function owner()
    {
        return $this->belongsTo(\App\User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany('App\User', 'workspace_members');
    }

    public function getLabelAttribute()
    {
        return $this->title;
    }
    
    public function users() {
        return $this->members()->getResults();
    }
}
