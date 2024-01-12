<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExplorerCode extends Model
{
    use HasFactory;

    protected $table = 'explorer_code';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'access_code',
        'usage',
        'is_active',
        'expiration'
    ];

    protected $with = [
        'users',
    ];

    protected $appends = [
        ''
    ];

    public function users()
    {
        return $this->belongsToMany('App\User', 'explorer_code_users');
    }
}
