<?php

namespace Zareismail\Task\Observers;


class TaskObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    // public $afterCommit = true;

    /**
     * Handle the User "created" event.
     *
     * @param  \Zareismail\Task\Models\Task  $task
     * @return void
     */
    public function created($task)
    {
        $task->notifyMembers('Created');
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \Zareismail\Task\Models\Task  $task
     * @return void
     */
    public function updated($task)
    {
        $task->notifyMembers('Updated');
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \Zareismail\Task\Models\Task  $task
     * @return void
     */
    public function deleted($task)
    {
        $task->notifyMembers('Deleted');
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \Zareismail\Task\Models\Task  $task
     * @return void
     */
    public function restored($task)
    {
        $task->notifyMembers('Restored');
    } 
}
