<?php

namespace Thor\Language;

/**
 * Language model test
 *
 */
class LanguageTest extends PackageTestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        $this->prepareDatabase();
    }

    public function testLanguagesTableHas5Records()
    {
        $this->assertCount(5, Language::all());
    }

    /**
     * @covers \Thor\Language\Language::scopeSorted
     */
    public function testScopeSorted()
    {
        $lang = Language::sorted()->first();
        $this->assertInstanceOf('Thor\\Language\\Language', $lang);
        $this->assertEquals('en', $lang->code);
        $this->assertEquals('en_US', $lang->locale);
        $this->assertEquals(true, $lang->is_active);
        $this->assertEquals(1, $lang->sorting);
    }

    /**
     * @covers \Thor\Language\Language::scopeActive
     */
    public function testScopeActive()
    {
        $lang = Language::find(2);
        $lang->is_active = false;
        $lang->save();

        $langs = Language::active()->get();
        $this->assertCount(4, $langs);
        $this->assertEquals('en', $langs->first()->code);
    }

    /**
     * @covers \Thor\Language\Language::scopeToAssoc
     */
    public function testScopeToAssoc()
    {
        $langs = Language::toAssoc();
        $this->assertCount(5, $langs);
        $this->assertArrayHasKey('es', $langs);
        $this->assertArrayHasKey('en', $langs);
    }

    /**
     * @covers \Thor\Language\Language::scopeByCode
     */
    public function testScopeByCode()
    {
        $lang = Language::byCode('en')->first();
        $this->assertInstanceOf('Thor\\Language\\Language', $lang);
        $this->assertEquals(1, $lang->id);
        $this->assertEquals('en', $lang->code);
        $this->assertEquals('en_US', $lang->locale);
    }

    /**
     * @covers \Thor\Language\Language::scopeByLocale
     */
    public function testScopeByLocale()
    {
        $lang = Language::byLocale('es_ES')->first();
        $this->assertInstanceOf('Thor\\Language\\Language', $lang);
        $this->assertEquals(2, $lang->id);
        $this->assertEquals('es', $lang->code);
        $this->assertEquals('es_ES', $lang->locale);
    }

}
