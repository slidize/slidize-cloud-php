<?php
namespace Slidize\Cloud\Sdk\Test\UseCases;

use PHPUnit\Framework\TestCase;
use Slidize\Cloud\Sdk\Api\SlidizeApi;

class RemoveMacrosTests extends TestCase
{
    public const testFile = "..\\..\\TestData\\macros.pptm";
    public function testRemoveMacros()
    {
        $api = new SlidizeApi();
        $response = $api->removeMacros(self::testFile);
        $this->assertNotNull($response);
    }

    public function testRemoveMacrosWithHttpInfo()
    {
        $api = new SlidizeApi();
        $response = $api->removeMacrosWithHttpInfo(self::testFile);
        $this->assertNotNull($response);
        $this->assertNotNull($response[0]);
        $this->assertTrue($response[1] == 200);
    }
}
