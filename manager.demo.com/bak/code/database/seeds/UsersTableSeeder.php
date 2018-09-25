<?php

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // create admin account
        $admin = [
            'name' => 'admin',
            'email' => 'admin@22dutech.com',
            'password' => bcrypt('123456789'),
            'remember_token' => str_random(10),
        ];

        $user = User::updateOrCreate(
            ['email' => 'admin@22dutech.com'],
            $admin
        );

        $info = factory(\App\Http\Model\AdminInfo::class)->create();

        $user->adminInfo()->save($info);


        // only seed test data for local
        if(\Illuminate\Support\Facades\Config::get('app.env') === 'local'){
//            factory(User::class, 24)->create();
        }

    }
}