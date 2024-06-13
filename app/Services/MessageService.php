<?php

namespace App\Services;

use App\Exceptions\InvalidMessageContentException;
use App\Jobs\SendMessageJob;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class MessageService
{
    // Validate and send a message to a single user
    public function validateAndSendMessageToSingleUser($data)
    {
        // Validate the input data
        $validator = Validator::make($data, [
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
            'sender_id' => 'required',
            'sender_name' => 'required',
            'message_type' => 'required',
        ]);

        // If validation fails, return an error message
        if ($validator->fails()) {
            return ['error' => $validator->errors()->first()];
        }

        // Encrypt the message content and dispatch the SendMessageJob
        $data['content'] = json_encode(['message_data' => Crypt::encryptString($data['content'])]);
        SendMessageJob::dispatch($data);

        return ['success' => 'Message sent successfully.'];
    }

    // Validate and send a message to a group
    public function validateAndSendMessageToGroup($data)
    {
        // Validate the input data
        $validator = Validator::make($data, [
            'group_id' => 'required|integer',
            'content' => 'required|string',
            'sender_id' => 'required|exists:users,id',
            'sender_name' => 'required',
            'message_type' => 'required',
            'group_name' => 'required',
        ]);

        // If validation fails, return an error message
        if ($validator->fails()) {
            return ['error' => $validator->errors()->first()];
        }

        // Encrypt the message content and dispatch the SendMessageJob
        $data['content'] = json_encode(['message_data' => Crypt::encryptString($data['content'])]);
        SendMessageJob::dispatch($data);

        return ['success' => 'Message sent successfully.'];
    }

    // Validate and send a message to all users
    public function validateAndSendMessageToAll($data)
    {
        // Validate the input data
        $validator = Validator::make($data, [
            'content' => 'required|string',
            'sender_id' => 'required|exists:users,id',
            'sender_name' => 'required',
            'message_type' => 'required',
        ]);

        // If validation fails, return an error message
        if ($validator->fails()) {
            return ['error' => $validator->errors()->first()];
        }

        // Encrypt the message content and dispatch the SendMessageJob
        $data['content'] = json_encode(['message_data' => Crypt::encryptString($data['content'])]);
        SendMessageJob::dispatch($data);

        return ['success' => 'Message sent successfully.'];
    }

    // Decrypt a message
    public function decryptMessage($encryptedMessage)
    {
        // Attempt to decrypt the message content
        $content = json_decode($encryptedMessage, true);
        if (!$content || !isset($content['message_data'])) {
            throw new InvalidMessageContentException();
        }
        $decryptedMessage = Crypt::decryptString($content['message_data']);

        return ['decryptedMessage' => $decryptedMessage];
    }
}
