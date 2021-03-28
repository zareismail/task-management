<?php

namespace Zareismail\Task\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Nova;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Fields\{ID, Badge, Text, Trix, Boolean, BelongsTo, MorphTo, HasMany};
use Zareismail\NovaContracts\Nova\User;
use Zareismail\NovaPriority\Nova\Priority;
use Zareismail\Task\Helper;

class Task extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Zareismail\Task\Models\Task::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'tracking_code';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'tracking_code',
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
        return [
            ID::make(__('ID'), 'id')
                ->sortable()
                ->canSee(function($request) {
                    return $request->user()->isDeveloper();
                }),

            Text::make(__('Task Number'), 'tracking_code')
                ->exceptOnForms(),

            // Text::make(__('Task Type'), function() {
            //     return forward_static_call([Nova::resourceForModel($this->taskable_type), 'singularLabel']);
            // })->onlyOnIndex(),

            Badge::make(__('Status'), 'marked_as')
                ->sortable()
                ->addTypes([ 
                    'draft' => 'bg-60 text-90',
                    'accepted'=> 'bg-info-light text-success-dark'
                ])
                ->map([ 
                    'published' => 'info',
                    'inprogress' => 'success',
                    'completed' => 'success',
                    'rejected' => 'danger',
                    'pending' => 'warning',
                ]),

            static::datetimeField(__('Created At'), 'created_at') 
                ->exceptOnForms()
                ->sortable(),

            static::datetimeField(__('Updated At'), 'updated_at') 
                ->onlyOnDetail(),

            BelongsTo::make(__('Higher Task'), 'task', static::class)
                ->withoutTrashed()
                ->nullable()
                ->sortable()
                ->canSee(function($request) {
                    return $request->viaResource() == static::uriKey();
                }),

            MorphTo::make(__('Task Type'), 'taskable')
                ->types(Helper::taskableResources()->all())
                ->showCreateRelationButton()
                ->withoutTrashed()
                // ->hideFromIndex()
                ->required()
                ->rules('required'),

            MorphTo::make(__('Member'), 'member')
                ->types([User::class, Team::class])
                ->showCreateRelationButton()
                ->withoutTrashed() 
                ->required()
                ->rules('required'),

            BelongsTo::make(__('Task Priority'), 'priority', Priority::class)
                ->showCreateRelationButton()
                ->withoutTrashed()
                ->sortable()
                ->required()
                ->rules('required'),

            Trix::make(__('Task Note'), 'note')
                ->withFiles('public')
                ->help(__('Write something if need to describe the working')),

            Boolean::make(__('Publish Task'), 'marked_as')
                ->onlyOnForms()
                ->hideWhenUpdating()
                ->default(true)
                ->fillUSing(function($request, $model, $attribute) {
                    if(boolval($request->get($attribute))) {
                        $model->asPublished();
                    }
                }),

            HasMany::make(__('Activities'), 'activities', Activity::class),

            HasMany::make(__('Tasks'), 'tasks', static::class)->canSee(function($request) {
                return $request->viaResource() == static::uriKey();
            }),
        ];
    } 

    /**
     * Determine if the current user can update the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToUpdate(Request $request)
    { 
        return $request instanceof ActionRequest || parent::authorizedToUpdate($request);
    }

    /**
     * Determine if the current user can delete the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToDelete(Request $request)
    {
        if (!(
            $this->resource->isCompleted() || 
            $this->resource->isRejected() || 
            $this->resource->isDrafted() 
        )) {
            return false;
        }

        return parent::authorizedToDelete($request);
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
        return $query
                    ->with('task', 'tasks')
                    ->with('taskable', function($morphTo) {
                        $morphTo->morphWith(Helper::morphs());
                    })
                    ->with('member', function($morphTo) {
                        $morphTo->morphWith([User::class, Team::class]);
                    });
    }

    /**
     * Authenticate the query for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function authenticateQuery(NovaRequest $request, $query)
    {
        return $query->where(function($query) use ($request) {
            $query->when(static::shouldAuthenticate($request), function($query) {
                $query->authenticate()->orWhere->hasMemberAccess();
            });
        });
    } 

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            Metrics\TasksPerType::make(),
            Metrics\TasksPerStatus::make(),
            Metrics\TasksPerPriority::make(),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            Filters\Status::make(),
            Filters\Priority::make(),
            Filters\Member::make(), 
            Filters\Type::make(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [ 
            Actions\AddNote::make()
                ->onlyOnDetail()
                ->showOnTableRow()
                ->canSee(function($request) {
                    if ($request instanceof ActionRequest) {
                        return true;  
                    }

                    return ! optional($this->resource)->isCompleted();
                }),

            Actions\Publish::make()
                ->onlyOnDetail()
                ->showOnTableRow()
                ->withoutConfirmation()
                ->canSee(function($request) {
                    if ($request instanceof ActionRequest) {
                        return true;  
                    }

                    return optional($this->resource)->isDrafted() && 
                           $request->user()->is(optional($this->resource)->auth);
                }),

            Actions\Accept::make()
                ->onlyOnDetail()
                ->showOnTableRow()
                ->canSee(function($request) {
                    if ($request instanceof ActionRequest) {
                        return true;  
                    }

                    return optional($this->resource)->isPublished() && 
                           $this->userCanRunAction($request, $this->resource);
                }),

            Actions\Progress::make()
                ->onlyOnDetail()
                ->showOnTableRow()
                ->canSee(function($request) {
                    if ($request instanceof ActionRequest) {
                        return true;  
                    }

                    return optional($this->resource)->isAccepted() && 
                           $this->userCanRunAction($request, $this->resource);
                }),

            Actions\Rejection::make()
                ->onlyOnDetail()
                ->showOnTableRow()
                ->canSee(function($request) {
                    if ($request instanceof ActionRequest) {
                        return true;  
                    }

                    return optional($this->resource)->isAccepted() && 
                           $this->userCanRunAction($request, $this->resource);
                }),

            Actions\ReferToAnother::make()
                ->onlyOnDetail()
                ->showOnTableRow()
                ->canSee(function($request) {
                    if ($request instanceof ActionRequest) {
                        return true;  
                    }

                    if (optional($this->resource)->isCompleted()) {
                        return false;
                    }

                    return $request->user()->is(optional($this->resource)->auth) ||
                           $this->userCanRunAction($request, $this->resource);
                }),

            Actions\Completion::make()
                ->onlyOnDetail()
                ->showOnTableRow()
                ->canSee(function($request) {
                    if ($request instanceof ActionRequest) {
                        return true;  
                    }

                    if (optional($this->resource)->isCompleted()) {
                        return false;
                    }

                    if (! $this->userCanRunAction($request, $this->resource)) {
                        return false;
                    }

                    if (optional($this->resource)->progressing()) {
                        return true;
                    }

                    if (is_null($this->resource)) {
                        return false;
                    }

                    return $this->resource->tasks()->where('marked_as', '!=', 'completed')->doesntExist();
                }),

            Actions\Subtask::make()
                ->onlyOnDetail()
                ->showOnTableRow()
                ->canSee(function($request) {
                    if ($request instanceof ActionRequest) {
                        return true;  
                    } 

                    if (! $this->userCanRunAction($request, $this->resource)) {
                        return false;
                    } 

                    return optional($this->resource)->progressing() || 
                           optional($this->resource)->isPending();
                }),
        ];
    }

    public function userCanRunAction(Request $request, $resource)
    {
        if (is_null($resource->member)) {
            return false;
        }

        if ($request->user()->is($resource->member)) {
            return true;
        } 
        
        return  $request->user()->referrers->filter->is($resource->member)->isNotEmpty() ||
                $request->user()->teams->filter->is($resource->member)->isNotEmpty();
    }
}
