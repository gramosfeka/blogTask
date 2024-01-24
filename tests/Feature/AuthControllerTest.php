<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser()
    {
        return User::factory()->create([
            'password' => Hash::make('password123'),
        ]);
    }

    public function testRegisterSuccessfully()
    {
        $userData = [
            'name' => 'Gramos Feka',
            'email' => 'gramosfeka@gmail.com.com',
            'password' => 'password123',
        ];

        $response = $this->json('POST', route('register'), $userData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'authorisation' => [
                    'token',
                    'type',
                ],
            ]);
    }

    public function testLoginSuccessfully()
    {
        $user = $this->createUser();

        $loginData = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        $response = $this->json('POST', route('login'), $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'authorisation' => [
                    'token',
                    'type',
                ],
            ]);
    }

    public function testLogoutSuccessfully()
    {
        $user = $this->createUser();
        $token = Auth::guard('api')->login($user);

        $response = $this->json('POST', route('logout'), [], ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Successfully logged out',
            ]);
    }

    public function testRefreshTokenSuccessfully()
    {
        $user = $this->createUser();
        $token = Auth::guard('api')->login($user);

        $response = $this->json('POST', route('refresh'), [], ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'authorisation' => [
                    'token',
                    'type',
                ],
            ]);
    }

    public function testUnauthorizedLogin()
    {
        $invalidLoginData = [
            'email' => 'invalid@example.com',
            'password' => 'invalidpassword',
        ];

        $response = $this->json('POST', route('login'), $invalidLoginData);

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Unauthorized',
            ]);
    }
}
