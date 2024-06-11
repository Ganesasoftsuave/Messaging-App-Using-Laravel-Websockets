<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserGroupMember;
use App\Models\MessageRecipient;
use Illuminate\Support\Facades\Crypt;

class UserService
{
    public function getDashboardData($userId)
    {
        $users = User::where('id', '!=', $userId)->get(['id', 'name']);
        $notificationCount = MessageRecipient::where('recipient_id', $userId)
            ->where('seen', 0)
            ->count();

        $userGroups = UserGroup::pluck('id', 'name')->map(function ($groupId, $groupName) use ($userId) {
            $isSubscribed = UserGroupMember::where('user_id', $userId)
                ->where('group_id', $groupId)
                ->value('is_subscribe');
            return [
                'id' => $groupId,
                'name' => $groupName,
                'is_subscribed' => $isSubscribed ?? false,
            ];
        });

        return compact('users', 'notificationCount', 'userGroups');
    }

    public function getMessageList($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            throw new \Exception('User not found');
        }

        $messages = $user->receivedMessages()
            ->with(['sender', 'group' => function ($query) {
                $query->select('id', 'name');
            }])
            ->orderByDesc('messages.created_at')
            ->get([
                'messages.type',
                'messages.content',
                'messages.sender_id',
                'messages.created_at',
                'group_id',
            ])
            ->map(function ($message) {
                $content = json_decode($message->content, true);
                $message->content = $content && isset($content['message_data']) ? Crypt::decryptString($content['message_data']) : "Invalid content";
                $message->sender_name = $message->sender->name;
                $message->group_name = $message->group ? $message->group->name : null;
                return $message;
            });

        $notificationCount = $user->receivedMessages()
            ->wherePivot('seen', 0)
            ->count();

        return [
            'messages' => $messages,
            'notificationCount' => $notificationCount,
        ];
    }

    public function updateNotificationCount($userId)
    {
        User::findOrFail($userId);
        MessageRecipient::where('recipient_id', $userId)->update(['seen' => 1]);

        return ['success' => true];
    }

    public function subscribeToGroup($userId, $groupId)
    {
        User::findOrFail($userId);

        $member = UserGroupMember::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->first();

        if ($member) {
            $member->is_subscribe = !$member->is_subscribe;
        } else {
            $member = new UserGroupMember();
            $member->user_id = $userId;
            $member->group_id = $groupId;
            $member->is_subscribe = true;
        }

        $member->save();

        return ['is_subscribed' => $member->is_subscribe];
    }
}
