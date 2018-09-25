<?php

use Illuminate\Database\Seeder;

class OutMoneyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        factory(\App\Http\Model\OutMoney::class, 15)->create();
    }
}
