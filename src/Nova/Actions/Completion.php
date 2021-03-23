<?php

namespace Zareismail\Task\Nova\Actions;
 
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\{ActionFields, Trix};

class Completion extends Action
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
        $models->first()->complete();
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [  
            Trix::make(__('Note'), 'note')
                ->withFiles('public'),
        ];
    }
}
