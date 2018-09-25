<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSAdminInfoTable extends Migration
{

    protected $table = 's_admin_info';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function(Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->default(0)->comment('账号id');
            $table->string('nickname','30')->comment('账号昵称');
            $table->integer('img_id')->default(0)->comment('管理员头像, hh_file.id	');
            $table->integer('status')->default(0)->comment('状态, 1:启用，0:禁用');
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
