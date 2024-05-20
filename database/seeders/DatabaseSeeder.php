<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\booking;
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
        \App\Models\booking::query()->create([
            'schedule_id' => '4',
            'user_id' => '14',
            'status'=>'đang chờ xác nhận',
            'booking_time' => '2024-05-21 09:00:00'
        ]);
    }
}
