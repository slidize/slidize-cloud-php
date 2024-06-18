<?php
namespace Slidize\Cloud\Sdk\Test\UseCases;

use PHPUnit\Framework\TestCase;
use Slidize\Cloud\Sdk\Api\SlidizeApi;

class RemoveAnnotationsTests extends TestCase
{
    public const testFile = "..\\..\\TestData\\test.pptx";
    public function testRemoveAnnotations()
    {
        $api = new SlidizeApi();
        $response = $api->removeAnnotations(self::testFile);
        $this->assertNotNull($response);
    }

    public function testRemoveAnnotationsWithHttpInfo()
    {
        $api = new SlidizeApi();
        $response = $api->removeAnnotationsWithHttpInfo(self::testFile);
        $this->assertNotNull($response);
        $this->assertNotNull($response[0]);
        $this->assertTrue($response[1] == 200);
    }
}
