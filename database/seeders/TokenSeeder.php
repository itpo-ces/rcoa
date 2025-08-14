<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Token;
use Illuminate\Support\Str;

class TokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate 1000 unique tokens
        for ($i = 0; $i < 2000; $i++) {
            Token::create([
                'token' => Str::random(16)
            ]);
        }
    }
}
