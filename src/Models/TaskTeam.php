<?php

namespace Zareismail\Task\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};  

class TaskTeam extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Query the realted User.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
    	return $this->belongsToMany(config('zareismail.user', User::class), 'task_team_user');
    }
}
