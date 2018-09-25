<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysLogTable extends Migration
{


    protected $table = 'sys_log';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function(Blueprint $table) {
            $table->increments('id');
            $table->string('timestamp','50');
            $table->text('logger')->comment();
            $table->string('level',20)->comment();
            $table->text('message')->comment();
            $table->string('thread',200)->comment();
            $table->string('file',200)->comment();
            $table->integer('line')->default(0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
