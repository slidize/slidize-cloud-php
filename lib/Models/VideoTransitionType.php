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
 * VideoTransitionType Class Doc Comment
 */
class VideoTransitionType
{
    /**
     * Possible values of this enum
     */
    public const NONE = 'None';

    public const RANDOM = 'Random';

    public const FROM_PRESENTATION = 'FromPresentation';

    public const FADE = 'Fade';

    public const DISTANCE = 'Distance';

    public const SLIDE_LEFT = 'SlideLeft';

    public const CIRCLE_CROP = 'CircleCrop';

    public const DISSOLVE = 'Dissolve';

    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::NONE,
            self::RANDOM,
            self::FROM_PRESENTATION,
            self::FADE,
            self::DISTANCE,
            self::SLIDE_LEFT,
            self::CIRCLE_CROP,
            self::DISSOLVE
        ];
    }
}


