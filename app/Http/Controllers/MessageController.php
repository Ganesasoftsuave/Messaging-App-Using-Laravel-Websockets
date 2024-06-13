<?php

namespace App\Http\Controllers;

use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }

    /**
     * Send a message to a single user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendMessageToSingleUser(Request $request): JsonResponse
    {
        try {
            $result = $this->messageService->validateAndSendMessageToSingleUser($request->all());
            if (isset($result['error'])) {
                return response()->json(['error' => $result['error']], 422);
            }

            return response()->json(['success' => $result['success']]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Something Went Wrong'], 500);
        }
    }

    /**
     * Send a message to a group of users.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendMessageToGroup(Request $request): JsonResponse
    {
        try {
            $result = $this->messageService->validateAndSendMessageToGroup($request->all());
            if (isset($result['error'])) {
                return response()->json(['error' => $result['error']], 422);
            }

            return response()->json(['success' => $result['success']]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Something Went Wrong'], 500);
        }
    }

    /**
     * Send a message to all users.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendMessageToAll(Request $request): JsonResponse
    {
        try {
            $result = $this->messageService->validateAndSendMessageToAll($request->all());
            if (isset($result['error'])) {
                return response()->json(['error' => $result['error']], 422);
            }

            return response()->json(['success' => $result['success']]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Something Went Wrong'], 500);
        }
    }

    /**
     * Decrypt a message.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function decryptMessage(Request $request): JsonResponse
    {
        try {
            $result = $this->messageService->decryptMessage($request->input('encryptedMessage'));
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to decrypt the message.'], 500);
        }
    }
}
