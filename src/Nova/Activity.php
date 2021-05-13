<?php

namespace Zareismail\Task\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Actions\ActionResource;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\{Text, Trix, Boolean, MorphToActionTarget, BelongsTo};
use Zareismail\NovaContracts\Nova\User;
use Zareismail\Fields\VoiceRecorder;
use Klepak\NovaRouterLink\RouterLink;

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

            Trix::make(__('Note'), function() {
                return data_get(unserialize($this->fields), 'note');
            })->alwaysShow(), 

            VoiceRecorder::make(__('Voice'), function() {  
                return data_get(unserialize($this->fields), 'voice');
            }), 
        ]);
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fieldsForIndex(Request $request)
    {  
        return [ 
            BelongsTo::make(__('Action Initiated By'), 'user', User::class),
            
            MorphToActionTarget::make(__('Action Target'), 'target'), 

            RouterLink::make(__('Action Name'), 'name', function ($value) {
                return __($value);
            })->route('detail', [
                'resourceName' => static::uriKey(), 
                'resourceId' => $this->getKey()
            ]),
            
            Resource::datetimeField(__('Action Happened At'), 'created_at')->exceptOnForms(),

            Text::make(__('Note'), function() {
                return data_get(unserialize($this->fields), 'note');
            })->onlyOnIndex()->asHtml(), 

            VoiceRecorder::make(__('Voice'), function() {  
                return data_get(unserialize($this->fields), 'voice');
            })->showOnIndex(data_get(unserialize($this->fields), 'voice') ? true : false), 
        ];
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
     * @param  string|null  $search
     * @param  array  $filters
     * @param  array  $orderings
     * @param  string  $withTrashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function buildIndexQuery(NovaRequest $request, $query, $search = null,
                                      array $filters = [], array $orderings = [],
                                      $withTrashed = TrashedStatus::DEFAULT)
    { 
        return parent::buildIndexQuery($request, $query)
                    ->where('actionable_type', $request->newViaResource()->getMorphClass())
                    ->where('actionable_id', $request->viaResourceId);
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->with('user');
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
