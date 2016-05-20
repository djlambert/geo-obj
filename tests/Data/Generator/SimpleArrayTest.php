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

use CrEOF\Geo\Obj\Data\Generator\SimpleArray;

/**
 * Class SimpleArrayTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class SimpleArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param mixed  $value
     * @param string $exceptionMessage
     *
     * @covers       \CrEOF\Geo\Obj\Data\Generator\SimpleArray::generate
     * @dataProvider unsupportedFormatData
     */
    public function testUnsupportedFormat($value, $exceptionMessage)
    {
        if (version_compare(\PHPUnit_Runner_Version::id(), '5.0', '>=')) {
            $this->expectException('CrEOF\Geo\Obj\Exception\UnsupportedFormatException');
            null !== $exceptionMessage && $this->expectExceptionMessage($exceptionMessage);
        } else {
            $this->setExpectedException('CrEOF\Geo\Obj\Exception\UnsupportedFormatException', $exceptionMessage);
        }

        $generator = new SimpleArray();

        $generator->generate($value);
    }

    /**
     * @param string $value
     * @param array  $expected
     *
     * @covers       \CrEOF\Geo\Obj\Data\Generator\SimpleArray::generate
     * @covers       \CrEOF\Geo\Obj\Data\Generator\SimpleArray::getDimension
     * @covers       \CrEOF\Geo\Obj\Data\Generator\SimpleArray::getDimensionFromType
     * @dataProvider goodSimpleArrayData
     */
    public function testGoodSimpleArray($value, array $expected)
    {
        $generator = new SimpleArray();
        $actual    = $generator->generate($value);

        self::assertEquals($expected, $actual);
    }

    /**
     * @param mixed  $value
     * @param string $exception
     * @param string $exceptionMessage
     *
     * @covers       \CrEOF\Geo\Obj\Data\Generator\SimpleArray::generate
     * @covers       \CrEOF\Geo\Obj\Data\Generator\SimpleArray::getDimensionFromType
     * @dataProvider badSimpleArrayData
     */
    public function testBadSimpleArray($value, $exception, $exceptionMessage)
    {
        if (version_compare(\PHPUnit_Runner_Version::id(), '5.0', '>=')) {
            $this->expectException($exception);
            null !== $exceptionMessage && $this->expectExceptionMessage($exceptionMessage);
        } else {
            $this->setExpectedException($exception, $exceptionMessage);
        }

        $generator = new SimpleArray();

        $generator->generate($value);
    }

    /**
     * @return array[]
     */
    public function unsupportedFormatData()
    {
        return [
            'geoString' => [
                'value'            => '79:56:55W 40:26:46N',
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

    /**
     * @return array[]
     */
    public function goodSimpleArrayData()
    {
        return [
            'simplePointTest'      => [
                'value'    => [
                    'value' => [0, 0],
                    'type'  => 'POINT'
                ],
                'expected' => [
                    'type'       => 'Point',
                    'value'      => [0, 0],
                    'srid'       => null,
                    'dimension'  => null,
                    'properties' => []
                ]
            ],
            'nullDimensionPointTest'      => [
                'value'    => [
                    'value'     => [0, 0],
                    'type'      => 'POINT',
                    'dimension' => null
                ],
                'expected' => [
                    'type'       => 'Point',
                    'value'      => [0, 0],
                    'srid'       => null,
                    'dimension'  => null,
                    'properties' => []
                ]
            ],
            'lowercaseDimensionPointTest'      => [
                'value'    => [
                    'value'     => [0, 0, 0],
                    'type'      => 'POINT',
                    'dimension' => 'z'
                ],
                'expected' => [
                    'type'       => 'Point',
                    'value'      => [0, 0, 0],
                    'srid'       => null,
                    'dimension'  => 'Z',
                    'properties' => []
                ]
            ],
            'pointDimensionNameTest'      => [
                'value'    => [
                    'value'     => [0, 0, 0],
                    'type'      => 'POINTM',
                ],
                'expected' => [
                    'type'       => 'Point',
                    'value'      => [0, 0, 0],
                    'srid'       => null,
                    'dimension'  => 'M',
                    'properties' => []
                ]
            ],
            'detectCountPointZTest'      => [
                'value'    => [
                    'value' => [0, 0, 0],
                    'type'  => 'POINT'
                ],
                'expected' => [
                    'type'       => 'Point',
                    'value'      => [0, 0, 0],
                    'srid'       => null,
                    'dimension'  => 'Z',
                    'properties' => []
                ]
            ],
            'detectCountPointZMTest'      => [
                'value'    => [
                    'value' => [0, 0, 0, 0],
                    'type'  => 'POINT'
                ],
                'expected' => [
                    'type'       => 'Point',
                    'value'      => [0, 0, 0, 0],
                    'srid'       => null,
                    'dimension'  => 'ZM',
                    'properties' => []
                ]
            ],
            'nested'      => [
                'value'    => [
                    'type'  => 'geometrycollection',
                    'value' => [
                        ['type'  => 'POINT', 'value' => [10, 10]],
                        ['type'  => 'POINT', 'value' => [30, 30, 30]],
                        ['type'  => 'LINESTRING', 'value' => [[15, 15], [20, 20]]]
                    ],
                ],
                'expected' => [
                    'type'       => 'GeometryCollection',
                    'value'      => [
                        ['type'  => 'POINT', 'value' => [10, 10]],
                        ['type'  => 'POINT', 'value' => [30, 30, 30]],
                        ['type'  => 'LINESTRING', 'value' => [[15, 15], [20, 20]]]
                    ],
                    'srid'       => null,
                    'dimension'  => null,
                    'properties' => []
                ]
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function badSimpleArrayData()
    {
        return [
            'dimensionMismatchPointTest'      => [
                'value'    => [
                    'value'     => [0, 0, 0],
                    'type'      => 'POINTZM',
                    'dimension' => 'Z'
                ],
                'exception' => 'CrEOF\Geo\Obj\Exception\RuntimeException',
                'exceptionMessage' => 'Specified dimension "Z" does not match type "POINTZM"'
            ],
        ];
    }
}
