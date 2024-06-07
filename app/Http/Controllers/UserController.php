<?php

namespace App\Http\Controllers;

use App\Jobs\SendMessageJob;
use App\Models\MessageRecipient;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserGroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
class UserController extends Controller
{
    public function dashboard(): View
    {
        $userId = auth()->id();
        $users = User::where('id', '!=', $userId)->get(['id', 'name']);
        $notificationCount = 0;
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

        return view('dashboard', compact('users', 'notificationCount', 'userGroups'));
    }

    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'receiver_id' => 'required|exists:users,id',
                'content' => 'required|string',
                'sender_id' => 'required',
                'sender_name' => 'required',
                'message_type' => 'required',
            ]);
    
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['error' => $errors->first()], 422);
            }
            $encryptedContent = Crypt::encryptString($request->input('content'));
            $data = $request->all();
            $data['content'] = $encryptedContent;
             SendMessageJob::dispatch($data);

            return response()->json(['success' => 'Message sent successfully.']);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Something Went Wrong'], 500);
        }
    }

    public function sendGroupMessage(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'group_id' => 'required|integer',
                'content' => 'required|string',
                'sender_id' => 'required|exists:users,id',
                'sender_name' => 'required',
                'message_type' => 'required',
                'group_name' => 'required',
            ]);
    
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['error' => $errors->first()], 422);
            }
            $encryptedContent = Crypt::encryptString($request->input('content'));
            $data = $request->all();
            $data['content'] = $encryptedContent;

            SendMessageJob::dispatch($data);
            return response()->json(['success' => 'Message sent successfully.']);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Something Went Wrong'], 500);
        }
    }
    public function sendMessageToAll(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'content' => 'required|string',
                'sender_id' => 'required|exists:users,id',
                'sender_name' => 'required',
                'message_type' => 'required',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['error' => $errors->first()], 422);
            }
            $encryptedContent = Crypt::encryptString($request->input('content'));
            $data = $request->all();
            $data['content'] = $encryptedContent;
            SendMessageJob::dispatch($data);
            return response()->json(['success' => 'Message sent successfully.']);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Something Went Wrong'], 500);
        }
    }
    public function getMessageList($userId): JsonResponse
    {
        try {
            $notificationCount = 0;
            $user = User::find($userId);
            if (!$user) {
                throw new \Exception('User not found');
            }
            $messages = $user->receivedMessages()
                ->with('sender', 'group')
                ->orderByDesc('messages.created_at') 
                ->get(['sender_name', 'group_name', 'type', 'content'])
                ->each(function ($message) {
                    $message->content = Crypt::decryptString($message->content);
                });
            $notificationCount = $user->receivedMessages()
                ->whereHas('recipients', function ($query) use ($userId) {
                    $query->where('recipient_id', $userId)
                        ->where('seen', 0);
                })
                ->count();
    
            return response()->json([
                'messages' => $messages,
                'notificationCount' => $notificationCount,
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch notifications messages.'
            ], 500);
        }
    }
    public function updateNotificationCount($userId): JsonResponse
    {
        try {
            User::findOrFail($userId);
            MessageRecipient::where('recipient_id', $userId)->update(['seen' => 1]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update notification count.'], 500);
        }
    }

    public function decryptMessage(Request $request): JsonResponse
    {
        try {
            $encryptedMessage = $request->input('encryptedMessage');
            $decryptedMessage = Crypt::decryptString($encryptedMessage);
            return response()->json(['decryptedMessage' => $decryptedMessage]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to decrypt the message.'], 500);
        }
    }

    public function subscribe(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_id' => 'required',
                'user_name' => 'required',
                'group_id' => 'required',
            ]);

            $userId = $request->input('user_id');
            $groupId = $request->input('group_id');
            User::findOrFail($userId);
            $member = UserGroupMember::where('user_id', $userId)
                ->where('group_id', $groupId)
                ->first();

            if ($member) {
                $member->is_subscribe = !$member->is_subscribe;
            } else {
                $member = new UserGroupMember();
                $member->user_id = $userId;
                $member->user_name = $request->input('user_name');
                $member->group_id = $groupId;
                $member->is_subscribe = true;
            }

            $member->save();

            return response()->json(['is_subscribed' => $member->is_subscribe]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to Subscribe the Group.'], 500);
        }

    }
}
