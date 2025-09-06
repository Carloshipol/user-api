<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['q','role','is_active']);
        $page = (int) $request->input('page', 1);
        $pageSize = (int) $request->input('page_size', 10);

        Log::info('API /users called', [
            'filters' => $filters,
            'page' => $page,
            'page_size' => $pageSize
        ]);

        $result = $this->userService->getUsers($filters, $page, $pageSize);

        return response()->json($result);
    }

    public function show($id): JsonResponse
    {
        Log::info("API /users/{$id} called");

        $user = $this->userService->getUserById((int) $id);

        if (!$user) {
            Log::warning("User not found", ['id' => $id]);
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }

        Log::info("User fetched successfully", ['id' => $id]);
        return response()->json(['data' => $user]);
    }
}