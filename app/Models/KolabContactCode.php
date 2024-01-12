<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KolabContactCode extends Model
{
    use HasFactory;

    protected $table = 'contact_code';

    protected $fillable = [
        'access_code',
        'usage',
        'is_active',
        'expiration'
    ];

    protected $with = [
        'users',
    ];

    public function workspaces()
    {
        return $this->belongsToMany('App\Workspace', 'contact_code_workspace');
    }
}
