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

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $messageData;

    const MESSAGE_INDIVIDUAL = 'individual';
    const MESSAGE_GROUP = 'group';
    const MESSAGE_ALL = 'all';

    public function __construct($messageData)
    {
        $this->messageData = $messageData;
    }

    public function handle(): void
    {
        DB::beginTransaction();
        try {
            switch ($this->messageData['message_type']) {
                case self::MESSAGE_INDIVIDUAL:
                    $this->handleIndividualMessage();
                    break;
                case self::MESSAGE_GROUP:
                    $this->handleGroupMessage();
                    break;
                case self::MESSAGE_ALL:
                    $this->handleAllUsersMessage();
                    break;
                default:
                    throw new \InvalidArgumentException("Invalid message type: {$this->messageData['message_type']}");
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error sending message: " . $e->getMessage());
        }
    }

    protected function handleIndividualMessage(): void
    {
        $sender = User::find($this->messageData['sender_id']);
        $message = $sender->messages()->create([
            'content' => $this->messageData['content'],
            'type' => self::MESSAGE_INDIVIDUAL,
        ]);
        $receiver = User::find($this->messageData['receiver_id']);
        $message->recipients()->create([
            'recipient_id' => $receiver->id,
            'seen' => 0,
            'seen_at' => Carbon::now(),
        ]);
        event(new OneToOneMessageEvent($this->messageData));
    }

    protected function handleGroupMessage(): void
    {
        $group = UserGroup::with('members')->find($this->messageData['group_id']);
        $subscribedUserIds = $group->members
            ->where('is_subscribe', true)
            ->where('user_id', '!=', $this->messageData['sender_id'])
            ->pluck('user_id');

        $message = $group->messages()->create([
            'sender_id' => $this->messageData['sender_id'],
            'content' =>$this->messageData['content'],
            'type' => self::MESSAGE_GROUP,
            'group_id' => $this->messageData['group_id'],
        ]);
        $subscribedUserIds->each(function ($recipientId) use ($message) {
            $message->recipients()->create([
                'recipient_id' => $recipientId,
                'seen' => 0,
                'seen_at' => Carbon::now(),
            ]);
            event(new GroupMessageEvent($this->messageData, $recipientId));
        });
    }

    protected function handleAllUsersMessage(): void
    {
        $users = User::where('id', '!=', $this->messageData['sender_id'])->get();
        $message = Message::create([
            'sender_id' => $this->messageData['sender_id'],
            'content' => $this->messageData['content'],
            'type' => self::MESSAGE_ALL,
        ]);
        foreach ($users as $user) {
            $message->recipients()->create([
                'recipient_id' => $user->id,
                'seen' => 0,
                'seen_at' => Carbon::now(),
            ]);
            event(new AllUsersMessageEvent($this->messageData , $user->id));
        }
    }
}
