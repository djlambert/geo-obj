<?php
/**
 * Copyright (C) 2016 Derek J. Lambert
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace CrEOF\Geo\Obj\Tests\Data\Generator;

use CrEOF\Geo\Obj\Data\Generator\GeoString;

/**
 * Class GeoStringTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class GeoStringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param mixed $value
     *
     * @covers       \CrEOF\Geo\Obj\Data\Generator\GeoString::generate
     * @dataProvider unsupportedFormatData
     */
    public function testUnsupportedFormat($value, $exceptionMessage)
    {
        if (version_compare(\PHPUnit_Runner_Version::id(), '5.0', '>=')) {
            $this->expectException('CrEOF\Geo\Obj\Exception\UnsupportedFormatException');
            $this->expectExceptionMessage($exceptionMessage);
        } else {
            $this->setExpectedException('CrEOF\Geo\Obj\Exception\UnsupportedFormatException', $exceptionMessage);
        }

        $generator = new GeoString();

        $generator->generate($value);
    }

    /**
     * @covers \CrEOF\Geo\Obj\Data\Generator\GeoString::__construct
     */
    public function testConstructor()
    {
        $refl = new \ReflectionClass('CrEOF\Geo\Obj\Data\Generator\GeoString');
        $prop = $refl->getProperty('parser');

        $prop->setAccessible(true);
        $prop->setValue(null);

        self::assertAttributeEmpty('parser', 'CrEOF\Geo\Obj\Data\Generator\GeoString');

        $generator = new GeoString();

        self::assertInstanceOf('CrEOF\Geo\Obj\Data\Generator\GeoString', $generator);
        self::assertAttributeInstanceOf('CrEOF\Geo\String\Parser', 'parser', 'CrEOF\Geo\Obj\Data\Generator\GeoString');
    }

    /**
     * @covers \CrEOF\Geo\Obj\Data\Generator\GeoString::generate
     */
    public function testGoodValue()
    {
        $expected  = [
            'type'       => 'Point',
            'value'      => [-79.948611111111106, 40.446111111111108],
            'srid'       => null,
            'dimension'  => null,
            'properties' => []
        ];
        $generator = new GeoString();

        $actual = $generator->generate('79:56:55W 40:26:46N');

        self::assertSame($expected, $actual);
    }

    /**
     * @return array[]
     */
    public function unsupportedFormatData()
    {
        return [
            'arrayValue' => [
                'value'            => ['type' => 'point'],
                'exceptionMessage' => null
            ],
            'wktValue'   => [
                'value'            => 'POINT(0 0)',
                'exceptionMessage' => null
            ],
            'wkbValue'   => [
                'value'            => pack('H*', '000000000100000000000000000000000000000000'),
                'exceptionMessage' => null
            ],
            'geoJson'  => [
                'value'            => '{"type":"Point","coordinates":[0,0]}',
                'exceptionMessage' => null
            ],
            'psudoXml'  => [
                'value'            => '<xml>',
                'exceptionMessage' => null
            ],
        ];
    }
}
