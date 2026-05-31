<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_requires_email(): void
    {
        $response = $this->post('/login', ['password' => 'password']);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    public function test_login_requires_password(): void
    {
        $response = $this->post('/login', ['email' => 'user@example.com']);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_login_shows_invalid_credentials_message(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

    public function test_admin_login_requires_email(): void
    {
        $response = $this->post('/admin/login', ['password' => 'password']);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    public function test_admin_login_requires_password(): void
    {
        $response = $this->post('/admin/login', ['email' => 'admin@example.com']);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_admin_login_shows_invalid_credentials_message(): void
    {
        User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }
}
