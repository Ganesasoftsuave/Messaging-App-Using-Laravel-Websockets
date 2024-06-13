<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class GroupMessageEvent
 *
 * This event is responsible for broadcasting a group message.
 */
class GroupMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Public property to hold message data
    public $messageData;

    // Prefix for the channel name
    public $channelNamePrefix = 'group';

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
        // Broadcasting on a private channel named 'group' followed by the group ID
        return new PrivateChannel($this->channelNamePrefix.'.'.$this->messageData['group_id']);
    }
}
