<?php

namespace Zareismail\Task\Nova\Actions;
  
use Illuminate\Support\Collection; 
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Actions\Action as NovaAction; 

class Action extends NovaAction
{  
    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handleForTasks(ActionFields $fields, Collection $models)
    {   
    	return tap($this->handle($fields, $models), function() use ($models) {
            $models->first()->notifyMembers($this->name());
    	});
    }
}
