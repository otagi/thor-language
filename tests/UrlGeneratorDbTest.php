<?php

namespace Thor\Language;

class UrlGeneratorDbTest extends UrlGeneratorTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->prepareDatabase();
        $this->app['config']->set('language::use_database', true);
        $this->app['translator']->resolve(); // resolve, now using DB
    }

}
