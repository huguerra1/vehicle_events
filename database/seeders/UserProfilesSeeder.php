<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;

class UserProfilesSeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function (User $user) {
            UserProfile::factory()->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
