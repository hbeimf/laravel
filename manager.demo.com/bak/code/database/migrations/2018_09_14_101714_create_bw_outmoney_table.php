<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBwOutmoneyTable extends Migration
{

    protected $table =  'bw_outmoney';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->unsigned()->default(0)->comment('用户ID');
            $table->integer('withdraw_config_id')->default(0)->comment('稽核模式,对应 bw_withdraw_money_config.id');
            $table->integer('manage_id')->unsigned()->default(0)->comment('操作员ID');
            $table->enum('pay_scheme', \App\Http\Model\OutMoney::$payScheme)->comment('MT:人工提出,MC:线下提现');
            $table->enum('withdraw_type',\App\Http\Model\OutMoney::$withdrawType)->comment('提出类型'); //
            $table->bigInteger('withdraw_money')->comment('提出金额');
            $table->bigInteger('withdraw_money_actual')->comment('实际提出金额');
            $table->integer('de_discount')->default(0)->comment('扣除优惠金额，单位分');
            $table->integer('de_audit')->default(0)->comment('扣除行政【稽核】费，单位分');
            $table->integer('de_service_charge')->default(0)->comment('扣除手续费');
            $table->integer('de_overtime_charge')->default(0)->comment('超出当天出款次数手续费');
            $table->boolean('is_first')->default(1)->comment('是否首次出款');
            $table->boolean('discount_removed')->default(0)->comment('是否扣除优惠, 1:是;0:否');
            $table->string('bank_name')->default('')->comment('出款银行，冗余保存');
            $table->string('bank_number')->default('')->comment('出款银行，冗余保存');
            $table->text('remark')->default('')->comment('备注');
            $table->tinyInteger('status')->defualt(0)->comment('状态,0未确认,-1,禁止,1已发放');
            $table->integer('lock_uid')->default(0)->comment('锁定的用户,只有锁定的用户可以操作');
            $table->timestamp('lock_at')->nullable()->comment('锁定时间,在多久之后会超时,其他用户也可以操作,一般10分钟后可以操作就可以了');
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
