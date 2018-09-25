<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatTestTable extends Migration
{
    protected $table = 'bw_test';

    public function up()
    {
        Schema::create($this->table, function(Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->defualt(0)->comment('account type');
            $table->string('user_name')->comment();
            $table->string('bank_name')->comment();
            $table->string('bank_number')->comment();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
