<?php

namespace Zareismail\Task\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\{ID, Text};

class Work extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Task\Models\TaskWork::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'label';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'label',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            ID::make(__('Work Name'), 'label')->sortable()->required(),
        ];
    }

    /**
     * Determine if the given resource is authorizable.
     *
     * @return bool
     */
    public static function authorizable()
    {
        return false;
    }

    /**
     * Determine if this resource is available for navigation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function availableForNavigation(Request $request)
    {
        return false;
    }
}
