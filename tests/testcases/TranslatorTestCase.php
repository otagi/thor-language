<?php

namespace Thor\Language;

class TranslatorTestCase extends PackageTestCase
{

    public function testLangFacadeIsSwapped()
    {
        $this->assertArrayHasKey('translator', $this->app);
        $this->assertArrayHasKey('thor.translator', $this->app);
        $this->assertInstanceOf('Thor\\Language\\Translator', $this->app['translator']);
    }

    public function testAutoresolveDisabled()
    {
        $this->app['config']->set('language::autoresolve', false);
        $this->prepareRequest('/');
        $translator = new Translator($this->app);
        $this->assertEquals(-1, $translator->id());
        $this->assertEquals('en', $translator->code());
        $this->assertEquals('en_US', $translator->locale());
        $this->assertEquals('en_US', $this->app['config']->get('app.locale'));
    }

    /**
     * @covers  \Thor\Language\Translator::resolve
     * @covers  \Thor\Language\Translator::code
     * @covers  \Thor\Language\Translator::id
     * 
     */
    public function testCanDetectDefaultLanguage()
    {
        $this->prepareRequest('/');
        $use_db = $this->app['config']->get('language::use_database');
        $this->assertEquals($use_db ? 1 : -1, $this->app['translator']->id());
        $this->assertEquals('en', $this->app['translator']->code());
        $this->assertEquals('en_US', $this->app['translator']->locale());
        $this->assertEquals('en_US', $this->app['config']->get('app.locale'));
    }

    /**
     * @covers  \Thor\Language\Translator::resolve
     * @covers  \Thor\Language\Translator::code
     * @covers  \Thor\Language\Translator::id
     * @covers  \Thor\Language\Translator::language
     * @covers  \Thor\Language\Translator::resolveFromConfig
     * @covers  \Thor\Language\Translator::getCodeFromSegment
     * @covers  \Thor\Language\Translator::resolveFromDb
     * @dataProvider langProvider
     */
    public function testCanDetectLanguage($langCode, $locale, $expectedId)
    {
        $this->prepareRequest('/' . $langCode . '/');
        $this->assertInstanceOf('Thor\\Language\\Language', $this->app['translator']->language());
        $use_db = $this->app['config']->get('language::use_database');
        $this->assertEquals($use_db ? $expectedId : -1, $this->app['translator']->id());
        $this->assertEquals($langCode, $this->app['translator']->code());
        $this->assertEquals($locale, $this->app['translator']->locale());
        $this->assertEquals($locale, $this->app['config']->get('app.locale'));
    }

    /**
     * @covers  \Thor\Language\Translator::isValidCode
     * @dataProvider langProvider2
     */
    public function testIsValidCode($langCode, $locale, $mustBeValidCode, $mustBeValidLocale)
    {
        $this->assertEquals($mustBeValidCode, $this->app['translator']->isValidCode($langCode));
    }

    /**
     * @covers  \Thor\Language\Translator::isValidLocale
     * @dataProvider langProvider2
     */
    public function testIsValidLocale($langCode, $locale, $mustBeValidCode, $mustBeValidLocale)
    {
        $this->assertEquals($mustBeValidLocale, $this->app['translator']->isValidLocale($locale));
    }

    /**
     * @covers  \Thor\Language\Translator::getActiveLanguages
     */
    public function testGetActiveLanguages()
    {
        $use_db = $this->app['config']->get('language::use_database');
        $activeLangs = $this->app['translator']->getActiveLanguages();
        $this->assertInstanceOf('Illuminate\\Database\\Eloquent\\Collection', $activeLangs);
        if($use_db) {
            $this->assertCount(5, $activeLangs);
        } else {
            $this->assertCount(0, $activeLangs);
        }
    }

    /**
     * @covers  \Thor\Language\Translator::getAvailableLocales
     */
    public function testGetAvailableLocales()
    {
        $locales = $this->app['translator']->getAvailableLocales();
        $this->assertTrue(is_array($locales));
        $this->assertCount(5, $locales);
        $this->assertEquals(array('en' => 'en_US', 'es' => 'es_ES', 'fr' => 'fr_FR', 'de' => 'de_DE', 'it' => 'it_IT'), $locales);
    }

    /**
     * @covers  \Thor\Language\Translator::setInternalLocale
     */
    public function testSetInternalLocale()
    {
        // implement test
    }

    /**
     * @covers  \Thor\Language\Translator::setLanguage
     */
    public function testSetLanguage()
    {
        // implement test
    }

    /**
     * @covers  \Thor\Language\Translator::getCodeFromHeader
     * @dataProvider acceptLanguageHeaderProvider
     */
    public function testResolveFromHeader($headerValue, $expectedLangCode)
    {
        $this->app['config']->set('language::use_header', true);
        $this->prepareRequest('/', 'GET', array(), array(), array(
            'HTTP_ACCEPT_LANGUAGE' => $headerValue
        ));
        $this->assertEquals($expectedLangCode, $this->app['translator']->code());
    }

    /**
     * @covers  \Thor\Language\Translator::resolveWith
     * @dataProvider langProvider2
     */
    public function testResolveWith($langCode, $locale, $mustBeValidCode, $mustBeValidLocale)
    {
        if($mustBeValidLocale) {
            $this->assertEquals($locale, $this->app['translator']->resolveWith($langCode)->locale);
        } else {
            $this->assertNotEquals($locale, $this->app['translator']->resolveWith($langCode)->locale);
        }
    }

    public function langProvider()
    {
        return array(
            array('en', 'en_US', 1),
            array('es', 'es_ES', 2),
            array('fr', 'fr_FR', 3),
            array('de', 'de_DE', 4),
            array('it', 'it_IT', 5)
        );
    }

    public function langProvider2()
    {
        return array(
            array('en', 'en_US', true, true),
            array('es', 'es_ES', true, true),
            array('fr', 'fr_FR', true, true),
            array('de', 'de_DE', true, true),
            array('it', 'it_IT', true, true),
            array('ca', 'ca_ES', false, false),
            array('pt', 'pt_BR', false, false)
        );
    }

    public function acceptLanguageHeaderProvider()
    {
        return array(
            array('en-us', 'en'),
            array('en-gb', 'en'),
            array('es', 'es'),
            array('es-mx', 'es'),
            array('ca,es,en-us', 'en'),
            array('pt, pt-pt', 'en'),
            array('fr-ch', 'fr'),
            array('ita', 'it'),
            array('de,en-us', 'de')
        );
    }

}
