<?php

namespace Zareismail\Task;

use Laravel\Nova\Nova;

class Helper 
{  
    /**
     * Return Nova's taskable resources.
     * 
     * @return \Laravel\Nova\ResourceCollection
     */
    public static function taskableResources()
    {
        return Nova::authorizedResources(app('request'))->filter(function($resource) { 
            return collect(class_implements($resource::newModel()))->contains(Contracts\Taskable::class); 
        })->values();
    } 

    /**
     * Return taskable morphs class.
     * 
     * @return \Laravel\Nova\ResourceCollection
     */
    public static function morphs()
    {
        return static::taskableResources()->map(function($resource) {
            return $resource::$model;
        })->all();
    } 
}
