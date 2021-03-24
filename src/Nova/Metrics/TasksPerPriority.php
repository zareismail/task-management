<?php

namespace Zareismail\Task\Nova\Metrics;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Zareismail\NovaPriority\Nova\Priority;
use Zareismail\Task\Nova\Task;

class TasksPerPriority extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        $priorities = Priority::newModel()->get();

        return $this->count($request, Task::buildIndexQuery($request, Task::newModel()), 'priority_id')
                    ->label(function($value) use ($priorities) {
                        return optional($priorities->find($value))->label;
                    })
                    ->colors($priorities->keyBy->getKey()->map->color->all());
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'tasks-per-priority';
    }
}
