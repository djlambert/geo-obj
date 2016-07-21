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

namespace CrEOF\Geo\Obj\Tests\Data\Formatter;

use CrEOF\Geo\Obj\Data\Formatter\WKB;

/**
 * Class WKBTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @covers \CrEOF\Geo\Obj\Data\Formatter\WKB
 */
class WKBTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConstructor()
    {
        new WKB();
    }

    /**
     * @covers                   \CrEOF\Geo\Obj\Data\Formatter\WKB::__construct
     * @expectedException        \CrEOF\Geo\Obj\Exception\UnexpectedValueException
     * @expectedExceptionMessage
     */
    public function testBadByteOrder()
    {
        new WKB(5);
    }

    /**
     * @covers                   \CrEOF\Geo\Obj\Data\Formatter\WKB::__construct
     * @expectedException        \CrEOF\Geo\Obj\Exception\UnexpectedValueException
     * @expectedExceptionMessage
     */
    public function testBadFlags()
    {
        new WKB(WKB::WKB_XDR, 56);
    }

    /**
     * @covers                   \CrEOF\Geo\Obj\Data\Formatter\WKB::__construct
     * @expectedException        \CrEOF\Geo\Obj\Exception\UnexpectedValueException
     * @expectedExceptionMessage
     */
    public function testBadUnsupportedActions()
    {
        new WKB(WKB::WKB_XDR, WKB::WKB_FLAG_NONE, 45);
    }

    /**
     * @param       $value
     * @param array $arguments
     * @param       $expected
     *
     * @dataProvider goodTestData
     */
    public function testGoodData($value, array $arguments, $expected)
    {
        $reflect   = new \ReflectionClass('CrEOF\Geo\Obj\Data\Formatter\WKB');
        /** @var WKB $formatter */
        $formatter = $reflect->newInstanceArgs($arguments);

        $actual = $formatter->format($value);

        self::assertEquals(pack('H*', $expected), $actual);
    }

    /**
     * @param       $value
     * @param array $arguments
     * @param       $expected
     *
     * @dataProvider badTestData
     */
    public function testBadData($value, array $arguments, $expected)
    {
        $reflect   = new \ReflectionClass('CrEOF\Geo\Obj\Data\Formatter\WKB');
        /** @var WKB $formatter */
        $formatter = $reflect->newInstanceArgs($arguments);

        if (version_compare(\PHPUnit_Runner_Version::id(), '5.0', '>=')) {
            $this->expectException($expected['exception']);
            $this->expectExceptionMessage($expected['message']);
        } else {
            $this->setExpectedException($expected['exception'], $expected['message']);
        }

        $formatter->format($value);
    }

    /**
     * @return array[]
     */
    public function goodTestData()
    {
        return [
            'testPointDefaultConstructor' => [
                'value'     => [
                    'type'      => 'POINT',
                    'value'     => [0, 0],
                    'srid'      => null,
                    'dimension' => null
                ],
                'arguments' => [],
                'expected'  => '000000000100000000000000000000000000000000'
            ],
            'testPointZXDR' => [
                'value'     => [
                    'type'      => 'POINT',
                    'value'     => [0, 0, 0],
                    'srid'      => null,
                    'dimension' => 'Z'
                ],
                'arguments' => [WKB::WKB_XDR, WKB::WKB_ENCODING_POSTGIS],
                'expected'  => '0080000001000000000000000000000000000000000000000000000000'
            ],
            'testPointZXDRWithSRID' => [
                'value'     => [
                    'type'      => 'POINT',
                    'value'     => [1, 2, 3],
                    'srid'      => 4326,
                    'dimension' => 'Z'
                ],
                'arguments' => [WKB::WKB_XDR, WKB::WKB_ENCODING_POSTGIS],
                'expected'  => '00A0000001000010E63FF000000000000040000000000000004008000000000000'
            ],
            'testLineStringDefaultConstructor' => [
                'value'     => [
                    'srid'  => null,
                    'type'  => 'LINESTRING',
                    'value' => [
                        [34.23, -87], [45.3, -92]
                    ],
                    'dimension' => null
                ],
                'arguments' => [],
                'expected'  => '00000000020000000240411D70A3D70A3DC055C000000000004046A66666666666C057000000000000'
            ],
            'testPolygonDefaultConstructor' => [
                'value'     => [
                    'srid'  => null,
                    'type'  => 'POLYGON',
                    'value' => [
                        [[0, 0], [10, 0], [10, 10], [0, 10], [0, 0]]
                    ],
                    'dimension' => null
                ],
                'arguments' => [],
                'expected'  => '000000000300000001000000050000000000000000000000000000000040240000000000000000000000000000402400000000000040240000000000000000000000000000402400000000000000000000000000000000000000000000'
            ],
            'testMultiPointDefaultConstructor' => [
                'value'     => [
                    'srid'  => null,
                    'type'  => 'MULTIPOINT',
                    'value' => [
                        [0, 0],
                        [10, 0],
                        [10, 10],
                        [0, 10]
                    ],
                    'dimension' => null
                ],
                'arguments' => [],
                'expected'  => '000000000400000004000000000100000000000000000000000000000000000000000140240000000000000000000000000000000000000140240000000000004024000000000000000000000100000000000000004024000000000000'
            ],
            'testMultiLineStringNDR' => [
                'value'     => [
                    'srid'  => null,
                    'type'  => 'MULTILINESTRING',
                    'value' => [
                        [[0, 0], [10, 0], [10, 10], [0, 10]],
                        [[5, 5], [7, 5], [7, 7], [5, 7]]
                    ],
                    'dimension' => null
                ],
                'arguments' => [WKB::WKB_NDR],
                'expected'  => '01050000000200000001020000000400000000000000000000000000000000000000000000000000244000000000000000000000000000002440000000000000244000000000000000000000000000002440010200000004000000000000000000144000000000000014400000000000001C4000000000000014400000000000001C400000000000001C4000000000000014400000000000001C40'
            ],
            'testMultiPolygonDefaultConstructor' => [
                'value'     => [
                    'srid'  => null,
                    'type'  => 'MULTIPOLYGON',
                    'value' => [
                        [
                            [[0, 0], [10, 0], [10, 10], [0, 10], [0, 0]],
                            [[5, 5], [7, 5], [7, 7], [5, 7], [5, 5]]
                        ],
                        [
                            [[1, 1], [3, 1], [3, 3], [1, 3], [1, 1]]
                        ]
                    ],
                    'dimension' => null
                ],
                'arguments' => [],
                'expected'  => '0000000006000000020000000003000000020000000500000000000000000000000000000000402400000000000000000000000000004024000000000000402400000000000000000000000000004024000000000000000000000000000000000000000000000000000540140000000000004014000000000000401C0000000000004014000000000000401C000000000000401C0000000000004014000000000000401C00000000000040140000000000004014000000000000000000000300000001000000053FF00000000000003FF000000000000040080000000000003FF0000000000000400800000000000040080000000000003FF000000000000040080000000000003FF00000000000003FF0000000000000'
            ]
        ];
    }

    /**
     * @return array[]
     */
    public function badTestData()
    {
        return [
            'testBadXDRNoZFlag' => [
                'value'      => [
                    'type'      => 'POINT',
                    'value'     => [1, 2, 3],
                    'srid'      => 4326,
                    'dimension' => 'Z'
                ],
                'arguments' => [WKB::WKB_XDR, WKB::WKB_ENCODING_POSTGIS],
                'expected'  => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\UnexpectedValueException',
                    'message'   => ''
                ]
            ],
            'testBadXDRNoSRIDFlag' => [
                'value'      => [
                    'type'      => 'POINT',
                    'value'     => [1, 2, 3],
                    'srid'      => 4326,
                    'dimension' => 'Z'
                ],
                'arguments' => [WKB::WKB_XDR, WKB::WKB_ENCODING_POSTGIS],
                'expected'  => [
                    'exception' => 'CrEOF\Geo\Obj\Exception\UnexpectedValueException',
                    'message'   => ''
                ]
            ]
        ];
    }
}
