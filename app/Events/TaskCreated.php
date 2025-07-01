<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;


class TaskCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    public $user_id;
    public $title;
    public $description;
    public $end_date;

    public function __construct($user_id, $title, $description, $end_date)
    {
        $this->user_id = $user_id;
        $this->title = $title;
        $this->description = $description;
        $this->end_date = $end_date;
    }
    public function broadcastOn()
    {
        return new Channel('task.' . $this->user_id);
        // return new Channel('tasks');
        // return new PrivateChannel('task.' . $this->user_id);
    }
    public function broadcastAs()
    {
        return 'TaskCreated';
    }
    public function broadcastWith()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'end_date' => $this->end_date,
        ];
    }
}