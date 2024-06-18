<?php
namespace Slidize\Cloud\Sdk\Test\UseCases;

use PHPUnit\Framework\TestCase;
use Slidize\Cloud\Sdk\Api\SlidizeApi;
use Slidize\Cloud\Sdk\Models\MergeOptions;

class MergeTests extends TestCase
{
    public const testFile = "..\\..\\TestData\\test.pptx";
    public const masterFile = "..\\..\\TestData\\master.pptx";

    public function testMerge()
    {
        $api = new SlidizeApi();
        $options = new MergeOptions();
        
        $options->setMasterFileName("master.pptx");
        $options->setExcludeMasterFile(false);
        $response = $api->merge("PDF", [self::testFile, self::masterFile], $options);
        $this->assertNotNull($response);
    }

    public function testMergeWithHttpInfo()
    {
        $api = new SlidizeApi();
        $options = new MergeOptions();
        
        $options->setMasterFileName("master.pptx");
        $options->setExcludeMasterFile(false);
        $response = $api->mergeWithHttpInfo("PDF", [self::testFile, self::masterFile], $options);
        $this->assertNotNull($response);
        $this->assertNotNull($response[0]);
        $this->assertTrue($response[1] == 200);
    }
}
