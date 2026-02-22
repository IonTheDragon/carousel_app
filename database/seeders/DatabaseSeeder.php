<?php

namespace Database\Seeders;

use App\Models\Lk\User;
use App\Models\Lk\Role;
use App\Models\Lk\RoleVsUser;
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

        $data = [
            ['title'=>'Админ', 'slug'=>'admin'],
            ['title'=>'Менеджер', 'slug'=>'manager'],
            ['title'=>'Оператор', 'slug'=>'operator'],
        ];

        Role::insert($data);

        $admin_role = Role::where('slug', 'admin')->first();        

        $role_vs_user = new RoleVsUser([
            'user_id' => $user->id,
            'role_id' => $admin_role->id
        ]);

        $role_vs_user->save();                
    }
}
