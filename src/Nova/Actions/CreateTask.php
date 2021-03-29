<?php

namespace Zareismail\Task\Nova\Actions;
 
use Illuminate\Support\Collection;
use Laravel\Nova\Nova; 
use Laravel\Nova\Fields\ActionFields;
use Zareismail\Task\Nova\Task;

class CreateTask extends Action
{  
    /**
     * Determine where the action redirection should be without confirmation.
     *
     * @var bool
     */
    public $withoutConfirmation = true;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    { 
        $resource = Nova::newResourceFromModel($models->first());

        return [
            'push' => [
                'name' => 'create',
                'params' => [
                    'resourceName' => Task::uriKey(),
                ],
                'query' => [
                    'viaResource' => $resource::uriKey(),
                    'viaResourceId' => $resource->getKey(),
                    'viaRelationship' => 'tasks',
                ],
            ],
        ];
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [ 
        ];
    }
}
