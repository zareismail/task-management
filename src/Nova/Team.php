<?php

namespace Zareismail\Task\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\{ID, Text};
use Benjacho\BelongsToManyField\BelongsToManyField;
use Zareismail\NovaContracts\Nova\User;

class Team extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Task\Models\TaskTeam::class;

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
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

            ID::make(__('Team Name'), 'name')->sortable()->required(),

            BelongsToManyField::make(__('Team Members'), 'members', User::class)
                ->required()
                ->rules('required'),
        ];
    }
}
