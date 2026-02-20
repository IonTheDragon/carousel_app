<?php

namespace Database\Seeders;

use App\Models\Lk\User;
use App\Models\Admin\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = new User([
            'name' => 'Ivan',
            'phone' => '89231280714',
            'password' => bcrypt('ionproject')
        ]);
        $user->save();

        $user->admin()->create();                
    }
}
