<?php 

namespace Zareismail\Task\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;

interface Taskable
{	
	/**
	 * Query the related Task.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\MorphOneOrMany
	 */
	public function tasks(): MorphOneOrMany;
}