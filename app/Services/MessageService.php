<?php

namespace App\Services;

use App\Exceptions\InvalidMessageContentException;
use App\Jobs\SendMessageJob;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class MessageService
{
    public function validateAndSendMessageToSingleUser($data)
    {
        $validator = Validator::make($data, [
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
            'sender_id' => 'required',
            'sender_name' => 'required',
            'message_type' => 'required',
        ]);

        if ($validator->fails()) {
            return ['error' => $validator->errors()->first()];
        }

        $data['content'] = json_encode(['message_data' => Crypt::encryptString($data['content'])]);
        SendMessageJob::dispatch($data);
        return ['success' => 'Message sent successfully.'];
    }

    public function validateAndSendMessageToGroup($data)
    {
        $validator = Validator::make($data, [
            'group_id' => 'required|integer',
            'content' => 'required|string',
            'sender_id' => 'required|exists:users,id',
            'sender_name' => 'required',
            'message_type' => 'required',
            'group_name' => 'required',
        ]);

        if ($validator->fails()) {
            return ['error' => $validator->errors()->first()];
        }

        $data['content'] = json_encode(['message_data' => Crypt::encryptString($data['content'])]);
        SendMessageJob::dispatch($data);

        return ['success' => 'Message sent successfully.'];
    }

    public function validateAndSendMessageToAll($data)
    {
        $validator = Validator::make($data, [
            'content' => 'required|string',
            'sender_id' => 'required|exists:users,id',
            'sender_name' => 'required',
            'message_type' => 'required',
        ]);

        if ($validator->fails()) {
            return ['error' => $validator->errors()->first()];
        }

        $data['content'] = json_encode(['message_data' => Crypt::encryptString($data['content'])]);
        SendMessageJob::dispatch($data);

        return ['success' => 'Message sent successfully.'];
    }

    public function decryptMessage($encryptedMessage)
    {
            $content = json_decode($encryptedMessage, true);
            if (!$content || !isset($content['message_data'])) {
                throw new InvalidMessageContentException();
            }
            $decryptedMessage = Crypt::decryptString($content['message_data']);
    
        return ['decryptedMessage' => $decryptedMessage];
    }
}
