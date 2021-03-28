<?php

namespace Zareismail\Task\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Nova\Actions\Actionable;
use Zareismail\NovaContracts\Models\AuthorizableModel;
use Zareismail\Contracts\Concerns\Trackable;

class Task extends AuthorizableModel
{
    use HasFactory, SoftDeletes, Trackable, Markable; 

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
    	'created_at' => 'datetime',
    	'updated_at' => 'datetime',
    	'deleted_at' => 'datetime',
    ];

    /**
     * Query the realted Task.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOneOrMany
     */
    public function tasks()
    {
    	return $this->hasMany(static::class);
    }

    /**
     * Query the realted Task.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo(static::class);
    }

    /**
     * Query the realted Priority.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function priority()
    {
        return $this->belongsTo(\Zareismail\NovaPriority\Models\NovaPriority::class);
    }

    /**
     * Query the realted User.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
    	return $this->morphTo();
    }

    /**
     * Query the realted Taskable.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function taskable()
    {
    	return $this->morphTo();
    }

    /**
     * Query where has member access.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder $query 
     */
    public function scopeHasMemberAccess($query)
    {
        return $query->member()->orWhere->substitute()->orWhere->team();
    }

    /**
     * Query the related TaskTeam.
     *  
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder $query 
     */
    public function scopeTeam($query)
    { 
        return $query->where(function($query) {
            $query->where('member_type', TaskTeam::class)
                  ->whereIn('member_id', request()->user()->teams->modelKeys());
        });
    }

    /**
     * Query the related User.
     *  
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder $query 
     */
    public function scopeMember($query)
    {  
        return $query->where(function($query) {
            $query->where('member_id', optional(request()->user())->getKey())
                  ->where('member_type', optional(request()->user())->getMorphClass());
        });
    }

    /**
     * Query the related User.
     *  
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder $query 
     */
    public function scopeSubstitute($query)
    {  
        return $query->where(function($query) {
            $query->whereIn('member_id', request()->user()->referrers->modelKeys())
                  ->where('member_type', optional(request()->user())->getMorphClass());
        });
    }

    /**
     * Get all of the action events for the user.
     */
    public function activities()
    {
        return $this->morphMany(\Laravel\Nova\Nova::actionEvent(), 'actionable');
    }

    /**
     * Refers a Task to another user.
     * 
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user 
     * @return $this       
     */
    public function referTo(Authenticatable $user)
    {
        return \DB::transaction(function() use ($user) {
            $this->tasks()->authenticate($this->member)->get()->each->referTo($user);

            return $this->member()->associate($user)->publish();
        }); 
    }
}
