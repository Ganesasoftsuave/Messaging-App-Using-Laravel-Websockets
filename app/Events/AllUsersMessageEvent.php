<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\Channel;

/**
 * Class AllUsersMessageEvent
 *
 * This event is responsible for broadcasting a message to all users.
 */
class AllUsersMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Public property to hold message data
    public $messageData;

    // Name of the channel to broadcast on
    public $channelName = 'allUser';

    /**
     * Create a new event instance.
     *
     * @param array $messageData The data of the message to be broadcasted.
     */
    public function __construct($messageData)
    {
        $this->messageData = $messageData;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // Broadcasting on a public channel named 'allUser'
        return new Channel($this->channelName);
    }
}
