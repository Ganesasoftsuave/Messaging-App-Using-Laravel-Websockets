<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function dashboard(): View
    {
        $userId = auth()->id();
        $data = $this->userService->getDashboardData($userId);
        return view('dashboard', $data);
    }

    public function getMessageList($userId): JsonResponse
    {
        try {
            $result = $this->userService->getMessageList($userId);

            return response()->json($result);
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
            $result = $this->userService->updateNotificationCount($userId);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update notification count.'], 500);
        }
    }

    public function subscribe(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_id' => 'required',
                'group_id' => 'required',
            ]);

            $result = $this->userService->subscribeToGroup($request->input('user_id'), $request->input('group_id'));

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to Subscribe the Group.'], 500);
        }
    }
}
