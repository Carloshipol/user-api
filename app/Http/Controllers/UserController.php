<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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

        $result = $this->userService->getUsers($filters, $page, $pageSize);

        return response()->json($result);
    }

    public function show($id): JsonResponse
    {
        $user = $this->userService->getUserById((int) $id);

        if (!$user) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }

        return response()->json(['data' => $user]);
    }
}