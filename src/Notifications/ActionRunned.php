<?php

namespace Zareismail\Task\Notifications;
 
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Mirovit\NovaNotifications\Notification as Messages;
use Zareismail\NovaContracts\Nova\Pusher;
use Zareismail\Task\Nova\Task;

class ActionRunned extends Notification
{ 
    /**
     * The action name.
     * 
     * @var array
     */
    public $actionName;

    /**
     * The Task instance.
     * 
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $task;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($actionName, $task)
    {
        $this->actionName = $actionName;
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return array_filter(['database', Pusher::enabled() ? 'broadcast' : null]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return Messages::make(__('Task :name Updated', [
                    'name' => with($taskResource = new Task($this->task), function($taskResource) {
                        return $taskResource->title();
                    }),
                ]), __('User :user runned action (:action) on the task :task', [
                    'user'  => request()->user()->fullname(),
                    'action'=> $this->actionName,
                    'task'  => $taskResource->title(),
                ])) 
                ->level('info')
                ->showMarkAsRead()
                ->toArray();
    }
}