<?php

namespace App\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class OneToOneMessageEvent
 *
 * This event is responsible for broadcasting a one-to-one message.
 */
class OneToOneMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Public property to hold message data
    public $messageData;

    // Prefix for the channel name
    public $channelNamePrefix = 'user';

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
        // Broadcasting on a private channel named 'user' followed by the receiver's ID
        return new PrivateChannel($this->channelNamePrefix.'.'.$this->messageData['receiver_id']);
    }
}
