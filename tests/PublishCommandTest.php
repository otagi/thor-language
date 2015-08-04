<?php

namespace Thor\Language;

/**
 *
 */
class PublishCommandTest extends PackageTestCase
{

    /**
     * 
     */
    public function testPublishLang()
    {
        $artisan = $this->app->make('artisan');

        // Publish lang
        // We need to specify the lang path here because the tests base_path() is the package src folder
        $artisan->call('lang:publish', array('package' => 'thor/language', '--path' => 'lang'));

        // Check files
        $this->assertFileExists($this->app['path'] . '/lang/packages/en/language/header.php');
        $this->assertFileExists($this->app['path'] . '/lang/packages/en/language/footer.php');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailPublishLang()
    {
        $artisan = $this->app->make('artisan');
        $artisan->call('lang:publish', array('package' => 'thor/language'));
    }

    /**
     * 
     */
    public function testPublishWithoutPath()
    {
        // reset base path to point to our package's src directory
        $this->app['path.base'] = realpath(dirname(__FILE__) . '/../');
        $artisan = $this->app->make('artisan');
        $artisan->call('lang:publish', array('package' => 'thor/language'));
    }

    /**
     * @depends testPublishLang
     */
    public function testAlternateLangLoader()
    {
        $this->testPublishLang();

        $this->assertEquals('Password reminder sent!', $this->app['translator']->trans('reminders.sent'));
        $this->assertEquals('Copyright 2014 Thor Framework', $this->app['translator']->trans('language::footer.copyright'));
        $this->assertEquals('Thor Framework', $this->app['translator']->trans('language::header.brand'));
        $this->assertEquals('Lorem Ipsum', $this->app['translator']->trans('language::header.subtitle'));

        // Remove packages folder
        $this->app['files']->deleteDirectory(realpath(__DIR__ . '/fixture/app/lang/packages/'));
    }

}
