<?php
namespace Slidize\Cloud\Sdk\Test\UseCases;

use PHPUnit\Framework\TestCase;
use Slidize\Cloud\Sdk\Api\SlidizeApi;
use Slidize\Cloud\Sdk\Models\ReplaceTextOptions;

class ReplaceTextTests extends TestCase
{
    public const testFile = "..\\..\\TestData\\test.pptx";
    public const oldValue = "Text to replace";
    public const newValue = "New value";

    public function testReplaceText()
    {
        $api = new SlidizeApi();
        $options = new ReplaceTextOptions();
        $options->setOldValue(self::oldValue);
        $options->setNewValue(self::newValue);
        $response = $api->replaceText($options, [self::testFile]);
        $this->assertNotNull($response);
    }

    public function testReplaceTextWithHttpInfo()
    {
        $api = new SlidizeApi();
        $options = new ReplaceTextOptions();
        $options->setOldValue(self::oldValue);
        $options->setNewValue(self::newValue);
        $response = $api->replaceTextWithHttpInfo($options, [self::testFile]);
        $this->assertNotNull($response);
        $this->assertNotNull($response[0]);
        $this->assertTrue($response[1] == 200);
    }
}
