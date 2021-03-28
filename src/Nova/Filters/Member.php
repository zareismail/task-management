<?php

namespace Zareismail\Task\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter; 
use Laravel\Nova\Nova; 
use Zareismail\Task\Nova\Team;

class Member extends Filter
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
        return $query->whereMemberType($value);
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

        return [
            $userResource::label() => $userResource::newModel()->getMorphClass(),
            Team::label() => Team::newModel()->getMorphClass(),
        ];
    }
}
