<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        foreach (range(1, 5) as $i) {
            User::factory()->create([
                'name' => "一般ユーザー{$i}",
                'email' => "user{$i}@example.com",
                'password' => Hash::make('password'),
            ]);
        }
    }
}
