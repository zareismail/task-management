<?php

namespace Zareismail\Task\Nova\Actions;
 
use Illuminate\Support\Collection; 
use Laravel\Nova\Fields\{ActionFields, Select};

class Accept extends Action
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
        $task = $models->first();
        $markAs = $fields->get('marked_as'); 
        
        $task->tasks()->exists() ? $task->asPending()->save() : $task->{$markAs}();
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Select::make(__('Task Status'), 'marked_as')
                ->required()
                ->rules('required')
                ->options([
                    'accept' => __('Accepted'),
                    'inprogress' => __('Inprogress')
                ]),
        ];
    }
}
