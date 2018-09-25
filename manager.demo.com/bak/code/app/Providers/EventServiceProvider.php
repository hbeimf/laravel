<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {
	/**
	 * The event listener mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [

		\Dingo\Api\Event\ResponseWasMorphed::class => [
			\App\Listeners\AddQueryLogToResponse::class,
		],
//		 \Illuminate\Foundation\Http\Events\RequestHandled::class => [
//		 	\App\Listeners\AddDBQueryLogToResponse::class,
//		 ],

	];

	/**
	 * Register any events for your application.
	 *
	 * @return void
	 */
	public function boot() {
		parent::boot();

		//
	}
}
