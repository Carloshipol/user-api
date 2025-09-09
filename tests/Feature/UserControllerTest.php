<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserControllerTest extends TestCase
{
    private $mockUsers;

    protected function setUp(): void
    {
        parent::setUp();
        // Carrega o mock do JSON
        $this->mockUsers = json_decode(file_get_contents(database_path('mock-users.json')), true);
    }

    /** @test */
    public function it_returns_users_with_default_pagination()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name', 'email', 'role', 'is_active']
                     ],
                     'pagination' => ['page', 'page_size', 'total']
                 ]);

        $this->assertCount(min(10, count($this->mockUsers)), $response['data']);
    }

    /** @test */
    public function it_returns_users_with_custom_pagination()
    {
        $response = $this->getJson('/api/users?page=2&page_size=5');

        $response->assertStatus(200)
                 ->assertJsonFragment(['page' => 2])
                 ->assertJsonFragment(['page_size' => 5]);
    }

    /** @test */
    public function it_can_filter_users_by_query_role_and_is_active()
    {
        $sampleUser = $this->mockUsers[0];

        $response = $this->getJson('/api/users?q=' . urlencode($sampleUser['name']) . '&role=' . $sampleUser['role'] . '&is_active=' . ($sampleUser['is_active'] ? '1' : '0'));

        $response->assertStatus(200);
        $this->assertNotEmpty($response['data']);
    }

    /** @test */
    public function it_returns_user_by_id_or_404()
    {
        $user = $this->mockUsers[0];

        // Testa usuário existente
        $response = $this->getJson("/api/users/{$user['id']}");
        $response->assertStatus(200)
                 ->assertJson([
                   'data' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'is_active' => $user['is_active'],
                    ]
                 ]);

        // Testa usuário inexistente
        $this->getJson('/api/users/999999')->assertStatus(404);
    }
}