<?php

namespace Zareismail\Task\Nova\Actions;
 
use Illuminate\Support\Collection; 
use Laravel\Nova\Fields\{ActionFields, Trix};
use Zareismail\Task\Nova\Task;

class Subtask extends Action
{ 
    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {  
        $models->first()->asPending()->save();

        return [
            'push' => [
                'name' => 'create',
                'query' => [
                    'viaResource' => Task::uriKey(),
                    'viaResourceId' => $models->first()->getKey(),
                    'viaRelationship' => 'tasks',
                ],
            ]
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
