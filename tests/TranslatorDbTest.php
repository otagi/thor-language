<?php

namespace Thor\Language;

class TranslatorDbTest extends TranslatorTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->prepareDatabase();
        $this->app['config']->set('language::use_database', true);
        $this->app['translator']->resolve(); // resolve, now using DB
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage The database has no active languages.
     */
    public function testExceptionDbIsEmpty()
    {
        $this->nukeDatabaseData();
        $this->prepareRequest('/');
    }

}
