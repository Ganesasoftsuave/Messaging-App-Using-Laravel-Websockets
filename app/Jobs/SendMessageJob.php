<?php

namespace App\Jobs;

use App\Events\AllUsersMessageEvent;
use App\Events\GroupMessageEvent;
use App\Events\OneToOneMessageEvent;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Enums\MessageType;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $messageData;

    public function __construct($messageData)
    {
        $this->messageData = $messageData;
    }

    public function handle(): void
    {
        DB::beginTransaction();
        try {
            // Determine the type of message and call the appropriate handler
            switch ($this->messageData['message_type']) {
                case MessageType::Individual():
                    $this->handleIndividualMessage();
                    break;
                case MessageType::Group():
                    $this->handleGroupMessage();
                    break;
                case MessageType::All():
                    $this->handleAllUsersMessage();
                    break;
                default:
                    // Throw an exception for invalid message types
                    throw new \InvalidArgumentException("Invalid message type: {$this->messageData['message_type']}");
            }
            // Commit the database transaction if all operations are successful
            DB::commit();
        } catch (\Exception $e) {
            // Rollback the transaction and log any errors
            DB::rollBack();
            Log::error("Error sending message: " . $e->getMessage());
        }
    }

    // Handle sending individual messages
    protected function handleIndividualMessage(): void
    {
        $sender = User::find($this->messageData['sender_id']);
        $message = $sender->messages()->create([
            'content' => $this->messageData['content'],
            'type' => MessageType::Individual(),
        ]);
        $receiver = User::find($this->messageData['receiver_id']);
        $message->recipients()->create([
            'recipient_id' => $receiver->id,
            'seen' => 0,
            'seen_at' => Carbon::now(),
        ]);
        // Trigger an event for one-to-one messages
        event(new OneToOneMessageEvent($this->messageData));
    }

    // Handle sending group messages
    protected function handleGroupMessage(): void
    {
        $group = UserGroup::with('members')->find($this->messageData['group_id']);
        $subscribedUserIds = $group->members
            ->where('is_subscribe', true)
            ->where('user_id', '!=', $this->messageData['sender_id'])
            ->pluck('user_id');

        $message = $group->messages()->create([
            'sender_id' => $this->messageData['sender_id'],
            'content' => $this->messageData['content'],
            'type' => MessageType::Group(),
            'group_id' => $this->messageData['group_id'],
        ]);
        $subscribedUserIds->each(function ($recipientId) use ($message) {
            $message->recipients()->create([
                'recipient_id' => $recipientId,
                'seen' => 0,
                'seen_at' => Carbon::now(),
            ]);
        });
        // Trigger an event for group messages
        event(new GroupMessageEvent($this->messageData));
    }

    // Handle sending messages to all users
    protected function handleAllUsersMessage(): void
    {
        $users = User::where('id', '!=', $this->messageData['sender_id'])->get();
        $message = Message::create([
            'sender_id' => $this->messageData['sender_id'],
            'content' => $this->messageData['content'],
            'type' => MessageType::All(),
        ]);
        foreach ($users as $user) {
            $message->recipients()->create([
                'recipient_id' => $user->id,
                'seen' => 0,
                'seen_at' => Carbon::now(),
            ]);
        }
        // Trigger an event for messages to all users
        event(new AllUsersMessageEvent($this->messageData));
    }
}
