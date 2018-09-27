<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider {
	/**
	 * Bootstrap any application services.
	 * https://blog.csdn.net/woqianduo/article/details/81782799
	 * https://blog.csdn.net/lbwo001/article/details/53063867
	 * @return void
	 */
	public function boot() {
		//
		Passport::routes();
		// accessToken有效期
		Passport::tokensExpireIn(Carbon::now()->addDays(15));
		// accessRefushToken有效期
		Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));

	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() {
		//
	}
}
