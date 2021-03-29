<?php

namespace Zareismail\Task\Nova\Actions;
 
use Illuminate\Support\Collection;
use Laravel\Nova\Nova; 
use Laravel\Nova\Fields\{ActionFields, Select, MorphTo, Textarea};
use Zareismail\NovaContracts\Nova\User;
use Zareismail\Task\Nova\Team;

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
        $models->first()->referTo($fields->member); 
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    { 
        $fields = [  
            MorphTo::make(__('Member'), 'member')
                ->types([User::class, Team::class])
                ->showCreateRelationButton()
                ->withoutTrashed() 
                ->required()
                ->rules('required'), 

            Textarea::make(__('Note'), 'note')
                ->nullable()
                ->rules('required'),
        ];

        if (request()->isMethod('get')) {
            return $fields;
        }

        array_shift($fields);

        return array_merge($fields, [
            Textarea::make(__('Member'), 'member')  
                ->rules('required')
                ->fillUsing(function($request, $model, $attribute) {
                    $resource = Nova::resourceForKey($request->get('member_type'));

                    $model->member($resource::newModel()->findOrFail($request->get('member')));
                }),
        ]); 
    }
}
