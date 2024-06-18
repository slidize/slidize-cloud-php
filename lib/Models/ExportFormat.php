<?php

/**
 * --------------------------------------------------------------------------------------------------------------------
 * <copyright company="Smallize">
 *   Copyright (c) 2024 Slidize for Cloud
 * </copyright>
 * <summary>
 *   Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 * 
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 * 
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 *  SOFTWARE.
 * </summary>
 * --------------------------------------------------------------------------------------------------------------------
 */


namespace Slidize\Cloud\Sdk\Models;
use \Slidize\Cloud\Sdk\ObjectSerializer;

/**
 * ExportFormat Class Doc Comment
 */
class ExportFormat
{
    /**
     * Possible values of this enum
     */
    public const ODP = 'Odp';

    public const OTP = 'Otp';

    public const PPTX = 'Pptx';

    public const PPTM = 'Pptm';

    public const POTX = 'Potx';

    public const PPT = 'Ppt';

    public const PPS = 'Pps';

    public const PPSM = 'Ppsm';

    public const POT = 'Pot';

    public const POTM = 'Potm';

    public const PDF = 'Pdf';

    public const XPS = 'Xps';

    public const PPSX = 'Ppsx';

    public const TIFF = 'Tiff';

    public const HTML = 'Html';

    public const SWF = 'Swf';

    public const TXT = 'Txt';

    public const DOC = 'Doc';

    public const DOCX = 'Docx';

    public const BMP = 'Bmp';

    public const JPEG = 'Jpeg';

    public const PNG = 'Png';

    public const EMF = 'Emf';

    public const WMF = 'Wmf';

    public const GIF = 'Gif';

    public const EXIF = 'Exif';

    public const ICO = 'Ico';

    public const SVG = 'Svg';

    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::ODP,
            self::OTP,
            self::PPTX,
            self::PPTM,
            self::POTX,
            self::PPT,
            self::PPS,
            self::PPSM,
            self::POT,
            self::POTM,
            self::PDF,
            self::XPS,
            self::PPSX,
            self::TIFF,
            self::HTML,
            self::SWF,
            self::TXT,
            self::DOC,
            self::DOCX,
            self::BMP,
            self::JPEG,
            self::PNG,
            self::EMF,
            self::WMF,
            self::GIF,
            self::EXIF,
            self::ICO,
            self::SVG
        ];
    }
}


