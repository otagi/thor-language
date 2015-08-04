<?php

namespace Thor\Language;

/**
 * Base Container-mocking class
 */
abstract class PackageTestCase extends AppTestCase
{

    protected function getPackageProviders()
    {
        return array('Thor\\Language\\LanguageServiceProvider');
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application    $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = realpath(__DIR__ . '/../../src');

        // load package config
        $config = include $app['path.base'] . '/config/config.php';
        foreach($config as $k => $v) {
            $app['config']->set('language::' . $k, $v);
        }

        // set default db to sqlite memory
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', array(
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ));
    }

    /**
     * Overrides the current request and resolves the language again
     * @param string $path
     * @param string $method
     * @param array $query
     * @param array $post
     * @param array $server
     * @return \Illuminate\Http\Request
     */
    protected function prepareRequest($path = '/', $method = 'GET', $query = array(), $post = array(), $server = array())
    {
        $req = parent::prepareRequest($path, $method, $query, $post, $server);
        // we need to resolve the language for each new request
        $this->app['translator']->resolve();
        return $req;
    }

    /**
     * Migrate and seed
     */
    protected function prepareDatabase()
    {
        // create an artisan object for calling migrations
        $artisan = $this->app->make('artisan');

        // Migrate and seed
        $artisan->call('migrate', array(
            '--database' => 'testbench',
            '--path' => 'migrations',
        ));
        $artisan->call('db:seed', array(
            '--class' => 'Thor\\Language\\LanguagesTableSeeder',
        ));
    }

    /**
     * Rollback migrations
     */
    protected function resetDatabase()
    {
        // create an artisan object for calling migrations
        $artisan = $this->app->make('artisan');

        // Migrate and seed
        $artisan->call('migrate:reset');
    }

    /**
     * Removes seeds
     */
    protected function nukeDatabaseData()
    {
        $this->resetDatabase();
        // create an artisan object for calling migrations
        $artisan = $this->app->make('artisan');
        $artisan->call('migrate', array(
            '--database' => 'testbench',
            '--path' => 'migrations',
        ));
    }

    public function setUp()
    {
        parent::setUp();

        $this->prepareRequest('/'); // set default request to GET /
    }

}
