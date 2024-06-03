<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\booking;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // \App\Models\booking::query()->create([
        //     'schedule_id' => '9',
        //     'user_id' => '7',
        //     'status'=>'đang chờ xác nhận',
        //     'booking_time' => '2024-05-21 09:00:00'
        // ]);
        // \App\Models\Service::query()->create([
        //     'categorie_id' => '1',
        //     'name' => '14',
        //     'price'=>'123',
        //     'describe' => 'aaaaaaaaaaa'
        // ]);
        \App\Models\User::query()->create([
            'email' => 'admin1@gmail.com',
            'role' => '0',
            'name'=>'Admin',
            'password' =>  Hash::make('12345678')
        ]);
        \App\Models\User::query()->create([
            'email' => 'admin2@gmail.com',
            'role' => '0',
            'name'=>'Admin',
            'password' =>  Hash::make('12345678')
        ]);
        \App\Models\User::query()->create([
            'email' => 'admin3@gmail.com',
            'role' => '0',
            'name'=>'Admin',
            'password' =>  Hash::make('12345678')
        ]);
        \App\Models\User::query()->create([
            'email' => 'admin4@gmail.com',
            'role' => '0',
            'name'=>'Admin',
            'password' =>  Hash::make('12345678')
        ]);
        \App\Models\User::query()->create([
            'email' => 'admin5@gmail.com',
            'role' => '0',
            'name'=>'Admin',
            'password' =>  Hash::make('12345678')
        ]);
        \App\Models\User::query()->create([
            'email' => 'user1@gmail.com',
            'role' => '1',
            'name'=>'user',
            'password' =>  Hash::make('12345678')
        ]);
        \App\Models\User::query()->create([
            'email' => 'user2@gmail.com',
            'role' => '1',
            'name'=>'user',
            'password' =>  Hash::make('12345678')
        ]);
        \App\Models\User::query()->create([
            'email' => 'user3@gmail.com',
            'role' => '1',
            'name'=>'user',
            'password' =>  Hash::make('12345678')
        ]);
        \App\Models\User::query()->create([
            'email' => 'user4@gmail.com',
            'role' => '1',
            'name'=>'user',
            'password' =>  Hash::make('12345678')
        ]);
        \App\Models\User::query()->create([
            'email' => 'user5@gmail.com',
            'role' => '1',
            'name'=>'user',
            'password' =>  Hash::make('12345678')
        ]);
        // \App\Models\User::factory(5)->create([
        // ]);


    }
}
