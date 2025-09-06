<?php

namespace App\Services;

use App\Repositories\UserRepository;

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

        return $this->userRepo->filterUsers(
            $page,
            $pageSize,
            $filters['q'] ?? null,
            $filters['role'] ?? null,
            $filters['is_active'] ?? null
        );
    }

    public function getUserById(int $id)
    {
        return $this->userRepo->findById($id);
    }
}