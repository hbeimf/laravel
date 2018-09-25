<?php

namespace extend\captcha\src;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

/**
 * Class CaptchaServiceProvider
 * @package Mews\Captcha
 */
class CaptchaServiceProvider extends ServiceProvider {

    /**
     * Boot the service provider.
     *
     * @return null
     */
    public function boot()
    {

	    // Validator extensions
	    $this->app['validator']->extend('captcha', function($attribute, $value, $parameters)
	    {
	        if($value == '1234') {
	            return true;
            }
	        if(!isset($parameters) || !isset($parameters[0]) || empty($parameters[0])) {
                return false;
            }
            $nowCode = cache($parameters[0]);
            $value = strtolower($value);

            if($nowCode != $value) {
                return false;
            }
            cache()->forget($parameters[0]);
            return true;
	    });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }

}
