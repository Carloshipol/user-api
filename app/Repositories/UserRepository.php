<?php

namespace App\Repositories;

class UserRepository
{
    private $users;

    public function __construct()
    {
        $path = database_path('mock-users.json');
        $this->users = json_decode(file_get_contents($path), true);
    }

    public function all()
    {
        return $this->users;
    }

    public function findById($id)
    {
        foreach ($this->users as $user) {
            if ($user['id'] == $id) {
                return $user;
            }
        }
        return null;
    }

    public function filterUsers($page, $pageSize, $q = null, $role = null, $isActive = null)
    {
        $results = $this->users;

        if ($q) {
            $results = array_filter($results, fn($u) => str_contains(strtolower($u['name']), strtolower($q)) || str_contains(strtolower($u['email']), strtolower($q)));
        }

        if ($role) {
            $results = array_filter($results, fn($u) => $u['role'] === $role);
        }

        if (!is_null($isActive)) {
            $bool = filter_var($isActive, FILTER_VALIDATE_BOOLEAN);
            $results = array_filter($results, fn($u) => $u['is_active'] === $bool);
        }

        $total = count($results);
        $totalPages = ceil($total / $pageSize);
        $page = max(min($page, $totalPages), 1);
        $offset = ($page - 1) * $pageSize;
        $data = array_slice($results, $offset, $pageSize);

        return [
            'data' => array_values($data),
            'pagination' => [
                'page' => $page,
                'page_size' => $pageSize,
                'total' => $total,
                'total_pages' => $totalPages
            ]
        ];
    }
}