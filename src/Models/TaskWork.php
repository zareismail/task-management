<?php

namespace Zareismail\Task\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Zareismail\Task\Contracts\Taskable;
use Zareismail\Task\Concerns\InteractsWithTasks;

class TaskWork extends Model implements Taskable
{
    use HasFactory, InteractsWithTasks;
}
