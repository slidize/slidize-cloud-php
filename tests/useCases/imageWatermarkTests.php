<?php
namespace Slidize\Cloud\Sdk\Test\UseCases;

use PHPUnit\Framework\TestCase;
use Slidize\Cloud\Sdk\Api\SlidizeApi;
use Slidize\Cloud\Sdk\Models\ImageWatermarkOptions;

class ImageWatermarkTests extends TestCase
{
    public const testFile = "..\\..\\TestData\\test.pptx";
    public const imageFile = "..\\..\\TestData\\watermark.png";

    public function testImageWatermark()
    {
        $api = new SlidizeApi();
        $options = new ImageWatermarkOptions();
        $options->setAngle(-45);
        $options->setZoom(50);

        $response = $api->imageWatermark([self::testFile], self::imageFile, $options);
        $this->assertNotNull($response);
    }

    public function testImageWatermarkWithHttpInfo()
    {
        $api = new SlidizeApi();
        $options = new ImageWatermarkOptions();
        $options->setAngle(-45);
        $options->setZoom(50);

        $response = $api->imageWatermarkWithHttpInfo([self::testFile], self::imageFile, $options);
        $this->assertNotNull($response);
        $this->assertNotNull($response[0]);
        $this->assertTrue($response[1] == 200);
    }
}
