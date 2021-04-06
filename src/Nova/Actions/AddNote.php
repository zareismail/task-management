<?php

namespace Zareismail\Task\Nova\Actions;
 
use Illuminate\Support\Collection; 
use Laravel\Nova\Fields\{ActionFields, Trix};
use Zareismail\Fields\VoiceRecorder;

class AddNote extends Action
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
                ->required()
                ->rules('required_without:voice'),

            VoiceRecorder::make(__('Voice'), 'voice')
                ->nullable(),
        ];
    }
}
