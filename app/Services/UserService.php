<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

class UserService
{
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getUsers(array $filters = [], int $page = 1, int $pageSize = 10)
    {
        $pageSize = min($pageSize, 50);

        Log::info('Fetching users', [
            'filters' => $filters,
            'page' => $page,
            'page_size' => $pageSize,
        ]);

        try {
            $users = $this->userRepo->filterUsers(
                $page,
                $pageSize,
                $filters['q'] ?? null,
                $filters['role'] ?? null,
                $filters['is_active'] ?? null
            );

            Log::info('Users fetched successfully', ['count' => count($users['data'])]);
            return $users;

        } catch (\Exception $e) {
            Log::error('Failed to fetch users', [
                'error' => $e->getMessage(),
                'filters' => $filters
            ]);
            throw $e;
        }
    }

    public function getUserById(int $id)
    {
        Log::info("Fetching user by ID", ['id' => $id]);

        $user = $this->userRepo->findById($id);

        if (!$user) {
            Log::warning("User not found", ['id' => $id]);
            return null; // ou lançar uma exceção personalizada, se quiser
        }

        Log::info("User fetched successfully", ['id' => $id]);
        return $user;
    }
}