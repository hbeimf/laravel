<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;   //新增的
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        DB::connection()->enableQueryLog();
        
        Schema::defaultStringLength(191);  //新增的

        /**
         * 验证手机号
         * */
        Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^1[34578][0-9]{9}$/', $value);
        }, '手机号码请输入11位数字号码');
        Validator::extend('alpha_dash_must', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(?=.{6,20})(?=.*[a-zA-Z_])(?=.*\d).([0-9a-zA-Z\_])*$/', $value);
        }, '请输入数字字母下划线组合');
        Validator::extend('alpha_dash_chinese', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9]+$/u', $value);
        }, '只能为数字、字母、汉字');
        
        \DB::listen(
            function ($sql) {
                foreach ($sql->bindings as $i => $binding) {
                    if ($binding instanceof \DateTime) {
                        $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                    } else {
                        if (is_string($binding)) {
                            $sql->bindings[$i] = "'$binding'";
                        }
                    }
                }
                $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);
                $query = vsprintf($query, $sql->bindings);
                $logFile = fopen(
                    storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_query.log'),
                    'a+'
                    );
                fwrite($logFile, date('Y-m-d H:i:s') . ': ' . $query . PHP_EOL);
                fclose($logFile);
            }
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
