<?php

namespace Zareismail\Task\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class Status extends Filter
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
        return $query->mark($value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return array_flip([
            'draft'     => 'Draft',
            'accepted'  => 'Accepted',
            'published' => 'Published',
            'inprogress'=> 'Inprogress',
            'completed' => 'Completed',
            'rejected'  => 'Rejected',
            'pending'   => 'Pending',
        ]);
    }
}
