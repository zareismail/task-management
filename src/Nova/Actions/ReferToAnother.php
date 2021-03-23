<?php

namespace Zareismail\Task\Nova\Actions;
 
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\{ActionFields, Select, Textarea};
use Zareismail\NovaContracts\Nova\User;

class ReferToAnother extends Action
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
        $models->first()->referTo(User::newModel()->findOrFail($fields->user)); 
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [ 
            Select::make(__('User'), 'user') 
                ->options(User::newModel()->whereKeyNot(request()->user()->id)->get()->mapInto(User::class)->keyBy->getKey()->map->title()->all())
                ->displayUsingLabels()
                ->searchable()
                ->required()
                ->rules('required'),

            Textarea::make(__('Note'), 'note')
                ->nullable()
        ];
    }
}
