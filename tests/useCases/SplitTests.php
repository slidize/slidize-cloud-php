<?php
namespace Slidize\Cloud\Sdk\Test\UseCases;

use PHPUnit\Framework\TestCase;
use Slidize\Cloud\Sdk\Api\SlidizeApi;
use Slidize\Cloud\Sdk\Models\SplitOptions;

class SplitTests extends TestCase
{
    public const testFile = "..\\..\\TestData\\test.pptx";
    public function testSplit()
    {
        $api = new SlidizeApi();
        $options = new SplitOptions();
        $options->setSlidesRange("1,2-4,5");
        $response = $api->split("PDF", self::testFile, $options);
        $this->assertNotNull($response);
    }

    public function testSplitWithHttpInfo()
    {
        $api = new SlidizeApi();
        $options = new SplitOptions();
        $options->setSlidesRange("1,2-4,5");
        $response = $api->splitWithHttpInfo("PDF", self::testFile, $options);
        $this->assertNotNull($response);
        $this->assertNotNull($response[0]);
        $this->assertTrue($response[1] == 200);
    }
}
