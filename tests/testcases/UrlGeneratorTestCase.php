<?php

namespace Thor\Language;

class UrlGeneratorTestCase extends PackageTestCase
{

    public function testURLFacadeIsSwapped()
    {
        $this->assertArrayHasKey('url', $this->app);
        $this->assertArrayHasKey('thor.url', $this->app);
        $this->assertInstanceOf('Thor\\Language\\UrlGenerator', $this->app['url']);
    }

    /**
     * @covers  \Thor\Language\UrlGenerator::langTo
     * @dataProvider requestPathProvider
     */
    public function testLangTo($requestPath, $expectedLangCode)
    {
        $this->prepareRequest($requestPath);
        $baseurl = $this->app['config']->get('app.url') . '/';
        // test the returned url
        $this->assertEquals($baseurl . $expectedLangCode . '/demo', $this->app['url']->langTo('demo'));
        // test if we can override the current lang code
        $this->assertEquals($baseurl . 'xx/demo', $this->app['url']->langTo('demo', array(), null, 'xx'));
    }

    /**
     * @covers  \Thor\Language\UrlGenerator::langSwitch
     * @dataProvider requestPathProvider
     */
    public function testLangSwitch($requestPath, $expectedLangCode, $noLangPath)
    {
        $this->prepareRequest($requestPath);
        $baseurl = $this->app['config']->get('app.url') . '/';
        $this->assertEquals($baseurl . 'xx' . $noLangPath, $this->app['url']->langSwitch('xx'));
    }

    public function requestPathProvider()
    {
        return array(
            array('/es/foo', 'es', '/foo'),
            array('/en/foobar', 'en', '/foobar'),
            array('/it/', 'it', ''),
            array('/de', 'de', ''),
            array('/fr/foo/bar/', 'fr', '/foo/bar'),
            array('/foo/', 'en', '/foo'),
            array('/fo/', 'en', '/fo')
        );
    }

}
