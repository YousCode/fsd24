<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $appends = ['talents'];

    public function talents()
    {
        return $this->belongsToMany('App\User', 'group_talent');
    }

    /**
     * @return Collection|mixed
     */
    public function getTalentsAttribute()
    {
        return $this->talents()->getResults();
    }
}
