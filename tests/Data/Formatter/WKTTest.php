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

use CrEOF\Geo\Obj\Data\Formatter\WKT;
use CrEOF\Geo\Obj\Data\DataFactory;

/**
 * Class WKTTest
 *
 * TODO: need tests for bad values
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 *
 * @covers \CrEOF\Geo\Obj\Data\Formatter\WKT
 */
class WKTTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider wktTestData
     */
    public function testWKTFormatter($value, $expected)
    {
        $formatter = new WKT();
        $actual    = $formatter->format($value);

        self::assertEquals($expected, $actual);
    }

    /**
     * @dataProvider wktTestData
     */
    public function testWKTDataFactoryFormat($value, $expected)
    {
        $actual = DataFactory::getInstance()->format($value, 'wkt');

        self::assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function wktTestData()
    {
        return [
            'testGoodPoint' => [
                'value' => [
                    'type'      => 'point',
                    'value'     => [0, 0],
                    'srid'      => null,
                    'dimension' => null
                ],
                'expected'   => 'POINT(0 0)'
            ],
            'testGoodLineString' => [
                'value' => [
                    'type'      => 'linestring',
                    'value'     => [[34.23, -87], [45.3, -92]],
                    'srid'      => null,
                    'dimension' => null
                ],
                'expected' => 'LINESTRING(34.23 -87,45.3 -92)'
            ] ,
            'testGoodPolygon' => [
                'value' => [
                    'type'      => 'polygon',
                    'value'     => [
                        [[0, 0], [10, 0], [10, 10], [0, 10], [0, 0]],
                        [[0, 0], [10, 0], [10, 10], [0, 10], [0, 0]]
                    ],
                    'srid'      => null,
                    'dimension' => null
                ],
                'expected' => 'POLYGON((0 0,10 0,10 10,0 10,0 0),(0 0,10 0,10 10,0 10,0 0))'
            ],
            'testGoodMultiLineString' => [
                'value' => [
                    'srid'      => null,
                    'type'      => 'MULTILINESTRING',
                    'value'     => [
                        [[0, 0], [10, 0], [10, 10], [0, 10]],
                        [[5, 5], [7, 5], [7, 7], [5, 7]]
                    ],
                    'dimension' => null
                ],
                'expected' => 'MULTILINESTRING((0 0,10 0,10 10,0 10),(5 5,7 5,7 7,5 7))'
            ],
            'testGoodMultiPolygon' => [
                'value' => [
                    'srid'       => null,
                    'type'       => 'MULTIPOLYGON',
                    'value'      => [
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
                'expected' => 'MULTIPOLYGON(((0 0,10 0,10 10,0 10,0 0),(5 5,7 5,7 7,5 7,5 5)),((1 1,3 1,3 3,1 3,1 1)))'
            ]
        ];
    }
}
