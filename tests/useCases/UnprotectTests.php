<?php
namespace Slidize\Cloud\Sdk\Test\UseCases;

use PHPUnit\Framework\TestCase;
use Slidize\Cloud\Sdk\Api\SlidizeApi;

class UnprotectTests extends TestCase
{
    public const testFile = "..\\..\\TestData\\protected.pptx";
    public const password = "password";
    public function testUnprotect()
    {
        $api = new SlidizeApi();
        $response = $api->unprotect(self::password, self::testFile);
        $this->assertNotNull($response);
    }

    public function testUnprotectWithHttpInfo()
    {
        $api = new SlidizeApi();
        $response = $api->unprotectWithHttpInfo(self::password, self::testFile);
        $this->assertNotNull($response);
        $this->assertNotNull($response[0]);
        $this->assertTrue($response[1] == 200);
    }
}
