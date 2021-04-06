<?php

namespace Zareismail\Task\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\{Text, Trix, Boolean};
use Laravel\Nova\Actions\ActionResource;
use Zareismail\Fields\VoiceRecorder;

class Activity extends ActionResource
{  
    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = [
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $fields = parent::fields($request);

        array_pop($fields);

        return array_merge($fields, [
            
            Resource::datetimeField(__('Action Happened At'), 'created_at')->exceptOnForms(),

            Text::make(__('Note'), function() {
                return data_get(unserialize($this->fields), 'note');
            })->onlyOnIndex()->asHtml(),

            Trix::make(__('Note'), function() {
                return data_get(unserialize($this->fields), 'note');
            })->alwaysShow(),

            Boolean::make('Has Voice', function() {  
                return data_get(unserialize($this->fields), 'voice') ? true : false;
            }),

            VoiceRecorder::make(__('Voice'), function() {  
                return data_get(unserialize($this->fields), 'voice');
            }), 
        ]);
    }
 

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Activities');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('Activity');
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuerys(NovaRequest $request, $query)
    {
        return $query->with('agent')->with('taskable', function($morphTo) {
            $morphTo->morphWith(Helper::morphs());
        });
    }

    /**
     * Authenticate the query for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function authenticateQuerys(NovaRequest $request, $query)
    {
        return $query->where(function($query) use ($request) {
            $query->when(static::shouldAuthenticate($request), function($query) {
                $query->authenticate()->orWhere->authenticateAgent();
            });
        });
    } 

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'task-activities';
    }
}
