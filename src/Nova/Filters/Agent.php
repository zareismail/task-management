<?php

namespace Zareismail\Task\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter; 
use Laravel\Nova\Nova; 

class Agent extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->whereAgentId($value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        $userResource = Nova::resourceForModel(config('zareismail.user'));

        return $userResource::newModel()->get()->mapInto($userResource)->keyBy->title()->map->getKey();
    }
}
