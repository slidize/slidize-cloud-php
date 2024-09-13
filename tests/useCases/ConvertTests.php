<?php
namespace Slidize\Cloud\Sdk\Test\UseCases;

use PHPUnit\Framework\TestCase;
use Slidize\Cloud\Sdk\Api\SlidizeApi;

class ConvertTests extends TestCase
{
    public const testFile = "..\\..\\TestData\\test.pptx";
    public function testConvert()
    {
        $api = new SlidizeApi();
        $response = $api->convert("PDF", [self::testFile]);
        $this->assertNotNull($response);
    }

    public function testConvertWithHttpInfo()
    {
        $api = new SlidizeApi();
        $response = $api->convertWithHttpInfo("PDF", [self::testFile]);
        $this->assertNotNull($response);
        $this->assertNotNull($response[0]);
        $this->assertTrue($response[1] == 200);
    }
}
