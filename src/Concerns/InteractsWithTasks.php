<?php 

namespace Zareismail\Task\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;

trait InteractsWithTasks
{ 
	/**
	 * Query the related Task.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\MorphOneOrMany
	 */
	public function tasks(): MorphOneOrMany
	{
		return $this->morphMany(\Zareismail\Task\Models\Task::class, 'taskable');
	}
}