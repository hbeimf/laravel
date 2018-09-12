<?php

namespace App\Providers;


use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Auth\AuthenticationException;
/**
 * Class ExceptionsServiceProvider - Hacky?!
 * @package App\Providers
 */
class ExceptionsServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(){}

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {



        app(\Dingo\Api\Exception\Handler::class)->register(function (AuthenticationException $exception) {
            throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException($exception->getMessage());
        });
        app(\Dingo\Api\Exception\Handler::class)->register(function (ModelNotFoundException $exception) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
        });

        app(\Dingo\Api\Exception\Handler::class)->register(function (ValidatorException $exception) {
            throw new BadRequestHttpException($exception->getMessageBag()->first());
        });




    }
}