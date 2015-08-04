<?php

namespace Thor\Language;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Illuminate\Config\Repository as Config;

abstract class AppTestCase extends \Illuminate\Foundation\Testing\TestCase
{

    public function setUp()
    {
        // No call to parent::setUp() from Illuminate\Foundation\Testing\TestCase
        $this->app = $this->createApplication();
        $this->app['files'] = new \Illuminate\Filesystem\Filesystem();
        $this->client = $this->createClient();
        $this->app->boot();
    }

    /**
     * Get application timezone.
     *
     * @return string
     */
    protected function getApplicationTimezone()
    {
        return 'UTC';
    }

    /**
     * Get application aliases.
     *
     * @return array
     */
    protected function getApplicationAliases()
    {
        return array(
            'App' => 'Illuminate\Support\Facades\App',
            'Artisan' => 'Illuminate\Support\Facades\Artisan',
            'Auth' => 'Illuminate\Support\Facades\Auth',
            'Blade' => 'Illuminate\Support\Facades\Blade',
            'Cache' => 'Illuminate\Support\Facades\Cache',
            'ClassLoader' => 'Illuminate\Support\ClassLoader',
            'Config' => 'Illuminate\Support\Facades\Config',
            'Controller' => 'Illuminate\Routing\Controller',
            'Cookie' => 'Illuminate\Support\Facades\Cookie',
            'Crypt' => 'Illuminate\Support\Facades\Crypt',
            'DB' => 'Illuminate\Support\Facades\DB',
            'Eloquent' => 'Illuminate\Database\Eloquent\Model',
            'Event' => 'Illuminate\Support\Facades\Event',
            'File' => 'Illuminate\Support\Facades\File',
            'Form' => 'Illuminate\Support\Facades\Form',
            'Hash' => 'Illuminate\Support\Facades\Hash',
            'HTML' => 'Illuminate\Support\Facades\HTML',
            'Input' => 'Illuminate\Support\Facades\Input',
            'Lang' => 'Illuminate\Support\Facades\Lang',
            'Log' => 'Illuminate\Support\Facades\Log',
            'Mail' => 'Illuminate\Support\Facades\Mail',
            'Paginator' => 'Illuminate\Support\Facades\Paginator',
            'Password' => 'Illuminate\Support\Facades\Password',
            'Queue' => 'Illuminate\Support\Facades\Queue',
            'Redirect' => 'Illuminate\Support\Facades\Redirect',
            'Redis' => 'Illuminate\Support\Facades\Redis',
            'Request' => 'Illuminate\Support\Facades\Request',
            'Response' => 'Illuminate\Support\Facades\Response',
            'Route' => 'Illuminate\Support\Facades\Route',
            'Schema' => 'Illuminate\Support\Facades\Schema',
            'Seeder' => 'Illuminate\Database\Seeder',
            'Session' => 'Illuminate\Support\Facades\Session',
            'SSH' => 'Illuminate\Support\Facades\SSH',
            'Str' => 'Illuminate\Support\Str',
            'URL' => 'Illuminate\Support\Facades\URL',
            'Validator' => 'Illuminate\Support\Facades\Validator',
            'View' => 'Illuminate\Support\Facades\View',
        );
    }

    /**
     * Get package aliases.
     *
     * @return array
     */
    protected function getPackageAliases()
    {
        return array();
    }

    /**
     * Get application providers.
     *
     * @return array
     */
    protected function getApplicationProviders()
    {
        return array(
            'Illuminate\Foundation\Providers\ArtisanServiceProvider',
            'Illuminate\Auth\AuthServiceProvider',
            'Illuminate\Cache\CacheServiceProvider',
            'Illuminate\Session\CommandsServiceProvider',
            'Illuminate\Foundation\Providers\ConsoleSupportServiceProvider',
            'Illuminate\Routing\ControllerServiceProvider',
            'Illuminate\Cookie\CookieServiceProvider',
            'Illuminate\Database\DatabaseServiceProvider',
            'Illuminate\Encryption\EncryptionServiceProvider',
            'Illuminate\Filesystem\FilesystemServiceProvider',
            'Illuminate\Hashing\HashServiceProvider',
            'Illuminate\Html\HtmlServiceProvider',
            'Illuminate\Log\LogServiceProvider',
            'Illuminate\Mail\MailServiceProvider',
            'Illuminate\Database\MigrationServiceProvider',
            'Illuminate\Pagination\PaginationServiceProvider',
            'Illuminate\Queue\QueueServiceProvider',
            'Illuminate\Redis\RedisServiceProvider',
            'Illuminate\Remote\RemoteServiceProvider',
            'Illuminate\Auth\Reminders\ReminderServiceProvider',
            'Illuminate\Database\SeedServiceProvider',
            'Illuminate\Session\SessionServiceProvider',
            'Illuminate\Translation\TranslationServiceProvider',
            'Illuminate\Validation\ValidationServiceProvider',
            'Illuminate\View\ViewServiceProvider',
        );
    }

    /**
     * Get package providers.
     *
     * @return array
     */
    protected function getPackageProviders()
    {
        return array();
    }

    /**
     * Get application paths.
     *
     * @return array
     */
    protected function getApplicationPaths()
    {
        $basePath = realpath(__DIR__ . '/../fixture');

        return array(
            'app' => "{$basePath}/app",
            'public' => "{$basePath}/public",
            'base' => $basePath,
            'storage' => "{$basePath}/app/storage",
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $app = new Application;

        $app->detectEnvironment(array(
            'local' => array('your-machine-name'),
        ));

        $app->bindInstallPaths($this->getApplicationPaths());

        $app['env'] = 'testing';

        $app->instance('app', $app);

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);

        $config = new Config($app->getConfigLoader(), $app['env']);
        $app->instance('config', $config);
        $app->startExceptionHandling();

        date_default_timezone_set($this->getApplicationTimezone());

        $aliases = array_merge($this->getApplicationAliases(), $this->getPackageAliases());
        AliasLoader::getInstance($aliases)->register();

        Request::enableHttpMethodParameterOverride();

        $providers = array_merge($this->getApplicationProviders(), $this->getPackageProviders());
        $app->getProviderRepository()->load($app, $providers);

        $this->getEnvironmentSetUp($app);

        return $app;
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application    $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Define your environment setup.
    }

    /**
     * 
     * @param string $path
     * @param string $method
     * @param array $query
     * @param array $post
     * @param array $server
     * @return \Illuminate\Http\Request
     */
    protected function prepareRequest($path = '/', $method = 'GET', $query = array(), $post = array(), $server = array())
    {
        $this->app['request'] = new \Illuminate\Http\Request($query, $post, array(), array(), array()
                , array_merge(array('REQUEST_URI' => $path, 'REQUEST_METHOD' => $method, 'SERVER_NAME' => 'localhost', 'HTTP_HOST' => 'localhost'), $server));
        return $this->app['request'];
    }

}
