<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


use App\Models\User; // Adjust namespace if your User model is elsewhere

class CheckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@gmail.com',
            // 'password' => bcrypt('password123'),
                'password' => Hash::make('password123'),]);
    }
}