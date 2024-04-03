<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Feature test for user registration.
     */
    public function testRegister()
    {


        $userData = [
            'name' => 'Test User',
            'email' => 'testtt@example.com',
            'password' => 'password',
            'role' => 'volunteer',
        ];

        $response = $this->json("POST", '/api/register', $userData);

        $response->assertStatus(200);

    }


    /**
     * Feature test for login.
     */
    public function testLogin()
    {
        $user = User::factory()->create();
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }
}
