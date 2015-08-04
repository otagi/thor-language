<?php

namespace Thor\Language;

use Illuminate\Support\Facades;
use Illuminate\Support\ServiceProvider;

class LanguageServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        Facades\Lang::swap($this->app['thor.translator']);
        Facades\Route::swap($this->app['thor.router']);
        Facades\URL::swap($this->app['thor.url']);

        $this->package('thor/language', 'language');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['config']->package('thor/language', realpath(__DIR__ . '/../../config'));
        $this->registerTranslator();
        $this->registerRouter();
        $this->registerUrlGenerator();
        $this->registerPublishCommand();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'thor.translator',
            'thor.router',
            'thor.url',
            'thor.language.publisher'
        );
    }

    /**
     *
     * @return void
     */
    protected function registerTranslator()
    {
        $this->app->bindShared('thor.translator', function($app) {
            return new Translator($app);
        });
    }

    /**
     *
     * @return void
     */
    protected function registerRouter()
    {
        $this->app->bindShared('thor.router', function($app) {
            return new Router($app['events'], $app);
        });
    }

    /**
     *
     * @return void
     */
    protected function registerUrlGenerator()
    {
        $this->app->bindShared('thor.url', function($app) {
            return new UrlGenerator($app['thor.router']->getRoutes(), $app->rebinding('request', function ($app, $request) {
                        $app['thor.url']->setRequest($request);
                    }));
        });
    }
    
    protected function registerPublishCommand(){
        $this->app->bindShared('thor.language.publisher', function($app) {
            return new Publisher($app['files'], $app['path'] . '/lang');
        });

        $this->app->bindShared('command.lang.publish', function($app) {
            return new PublishCommand($app['thor.language.publisher']);
        });

        $this->commands(array(
            'command.lang.publish'
        ));
    }

}
