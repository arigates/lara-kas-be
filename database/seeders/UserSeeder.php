<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Ari Sudarma',
                'email' => 'arisudarma@gmail.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($data as $dt) {
            $user = User::create($dt);

            $user->companies()->create([
                'name' => 'Lancar Jaya',
            ]);
        }
    }
}
