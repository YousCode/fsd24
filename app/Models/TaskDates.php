<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDates extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_dates';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $appends = ['taskParent'];

    /**
     * @return BelongsTo
     */
    public function taskParent()
    {
        return $this->belongsTo('App\Models\Task');
    }

    /**
     * @return Model|BelongsTo|object|null
     */
    public function getTaskParentAttribute()
    {
        return $this->taskParent()->first();
    }
}
