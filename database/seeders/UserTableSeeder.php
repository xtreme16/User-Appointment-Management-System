<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserTableSeeder extends Seeder
{
    public function run(): void
    {
        $timezones = [
            'Asia/Jakarta',
            'Asia/Makassar',
            'Asia/Jayapura',
            'UTC'
        ];

        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'id' => (string) Str::uuid(),
                'name' => "User {$i}",
                'username' => "user{$i}",
                'preferred_timezone' => $timezones[array_rand($timezones)],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}