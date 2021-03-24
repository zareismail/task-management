<?php

namespace Zareismail\Task\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter; 
use Zareismail\NovaPriority\Nova\Priority as PriorityResource;

class Priority extends Filter
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
        return $query->wherePriorityId($value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    { 
        return PriorityResource::newModel()->get()->mapInto(PriorityResource::class)->keyBy->title()->map->getKey();
    }
}
