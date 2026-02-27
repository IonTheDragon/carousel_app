<?php

namespace Database\Seeders;

use App\Models\Lk\User;
use App\Models\Lk\Role;
use App\Models\Lk\RoleVsUser;
use App\Models\Common\Option;
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
            'login' => 'IonTheDragon',
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

        $data = [
            ['title'=>'URL приложения', 'slug'=>'app_url', 'value' => ''],
            ['title'=>'VK ID приложения', 'slug'=>'vk_client_id', 'value' => ''],
            ['title'=>'VK Строка состояния', 'slug'=>'vk_state', 'value' => $this->generateRandomString(32)],
            ['title'=>'Yandex ID приложения', 'slug'=>'ya_client_id', 'value' => ''],
            ['title'=>'Yandex Secret приложения', 'slug'=>'ya_client_secret', 'value' => ''],
        ];

        Option::insert($data);                       
    }

    function generateRandomString($length = 32) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $index = random_int(0, $charactersLength - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }    
}
