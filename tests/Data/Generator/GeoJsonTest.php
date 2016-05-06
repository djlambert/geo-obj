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

use CrEOF\Geo\Obj\Data\Generator\GeoJson;

/**
 * Class GeoJsonTest
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class GeoJsonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param mixed $value
     *
     * @covers            \CrEOF\Geo\Obj\Data\Generator\GeoJson::generate
     * @dataProvider      unsupportedFormatData
     */
    public function testUnsupportedFormat($value, $exceptionMessage)
    {
        if (version_compare(\PHPUnit_Runner_Version::id(), '5.0', '>=')) {
            $this->expectException('CrEOF\Geo\Obj\Exception\UnsupportedFormatException');
            $this->expectExceptionMessage($exceptionMessage);
        } else {
            $this->setExpectedException('CrEOF\Geo\Obj\Exception\UnsupportedFormatException', $exceptionMessage);
        }

        $generator = new GeoJson();

        $generator->generate($value);
    }

    /**
     * @param mixed $value
     *
     * @covers            \CrEOF\Geo\Obj\Data\Generator\GeoJson::generate
     * @covers            \CrEOF\Geo\Obj\Data\Generator\GeoJson::getJsonError
     * @dataProvider      badJsonData
     */
    public function testBadJson($value, $exceptionMessage)
    {
        if (version_compare(\PHPUnit_Runner_Version::id(), '5.0', '>=')) {
            $this->expectException('CrEOF\Geo\Obj\Exception\UnexpectedValueException');
            $this->expectExceptionMessage($exceptionMessage);
        } else {
            $this->setExpectedException('CrEOF\Geo\Obj\Exception\UnexpectedValueException', $exceptionMessage);
        }

        $generator = new GeoJson();

        $generator->generate($value);
    }

    /**
     * @param string $value
     * @param array  $expected
     *
     * @covers       \CrEOF\Geo\Obj\Data\Generator\GeoJson::generate
     * @covers       \CrEOF\Geo\Obj\Data\Generator\GeoJson::getValueFromGeometry
     * @dataProvider goodJsonData
     */
    public function testGoodJson($value, array $expected)
    {
        $generator = new GeoJson();
        $actual    = $generator->generate($value);

        self::assertEquals($expected, $actual);
    }

    /**
     * @covers            \CrEOF\Geo\Obj\Data\Generator\GeoJson::generate
     * @expectedException \CrEOF\Geo\Obj\Exception\UnexpectedValueException
     */
    public function testBadType()
    {
        $generator = new GeoJson();

        $generator->generate('{"type":"Bad"}');
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
            'geoString'  => [
                'value'            => '79:56:55W 40:26:46N',
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
    public function badJsonData()
    {
        return [
            'depthError' => [
                'value'            => str_repeat('{"object":', 1024),
                'exceptionMessage' => 'Maximum stack depth exceeded'
            ],
            'stateMismatchError' => [
                'value'            => '{"foo":"bar"}}',
                'exceptionMessage' => 'Underflow or the modes mismatch'
            ],
            'controlCharacterError' => [
                'value'            => '{' . chr(1),
                'exceptionMessage' => 'Unexpected control character found'
            ],
            'syntaxError' => [
                'value'            => "{'foo':'bar'}",
                'exceptionMessage' => 'Syntax error, malformed JSON'
            ],
            'utf8Error' => [
                'value'            => "\x7b\xe2\x82\xff",
                'exceptionMessage' => 'Malformed UTF-8 characters, possibly incorrectly encoded'
            ]
        ];
    }

    /**
     * @return array[]
     */
    public function goodJsonData()
    {
        return [
            'pointTest' => [
                'value'    => '{"type":"Point","coordinates":[0,0]}',
                'expected' => [
                    'type'       => 'Point',
                    'value'      => [0, 0],
                    'srid'       => null,
                    'dimension'  => null,
                    'properties' => []
                ]
            ],
            'lineStringTest' => [
                'value'    => '{"type":"LineString","coordinates":[[0,0],[1,1],[2,2]]}',
                'expected' => [
                    'type'       => 'LineString',
                    'value'      => [[0, 0], [1, 1], [2, 2]],
                    'srid'       => null,
                    'dimension'  => null,
                    'properties' => []
                ]
            ],
            'polygonTest' => [
                'value'    => '{"type":"Polygon","coordinates":[[0,0],[1,1],[2,2],[0,0]]}',
                'expected' => [
                    'type'       => 'Polygon',
                    'value'      => [[0, 0], [1, 1], [2, 2],[0, 0]],
                    'srid'       => null,
                    'dimension'  => null,
                    'properties' => []
                ]
            ],
            'multiPointTest' => [
                'value'    => '{"type":"MultiPoint","coordinates":[[0,0],[1,1],[2,2]]}',
                'expected' => [
                    'type'       => 'MultiPoint',
                    'value'      => [[0, 0], [1, 1], [2, 2]],
                    'srid'       => null,
                    'dimension'  => null,
                    'properties' => []
                ]
            ],
            'multiLineStringTest' => [
                'value'    => '{"type":"MultiLineString","coordinates":[[[0,0],[1,1],[2,2]],[[4,4],[5,5],[6,6]]]}',
                'expected' => [
                    'type'       => 'MultiLineString',
                    'value'      => [
                        [[0, 0], [1, 1], [2, 2]],
                        [[4, 4], [5, 5], [6, 6]]
                    ],
                    'srid'       => null,
                    'dimension'  => null,
                    'properties' => []
                ]
            ],
            'multiPolygonTest' => [
                'value'    => '{"type":"MultiPolygon","coordinates":[[[0,0],[1,1],[2,2],[0,0]],[[4,4],[5,5],[6,6],[4,4]]]}',
                'expected' => [
                    'type'       => 'MultiPolygon',
                    'value'      => [
                        [[0, 0], [1, 1], [2, 2], [0, 0]],
                        [[4, 4], [5, 5], [6, 6], [4, 4]]
                    ],
                    'srid'       => null,
                    'dimension'  => null,
                    'properties' => []
                ]
            ],
            'featureTest' => [
                'value'    => '{"type":"Feature","geometry":{"type":"Point","coordinates":[0,0]},"properties":{"name":"null spot"}}',
                'expected' => [
                    'type'       => 'Feature',
                    'value'      => [
                        'type'       => 'Point',
                        'value'      => [0, 0]
                    ],
                    'srid'       => null,
                    'dimension'  => null,
                    'properties' => [
                        'name' => 'null spot'
                    ]
                ]
            ],
            'featureCollectionTest' => [
                'value'    => '{"type":"FeatureCollection","features":[{"type":"Feature","geometry":{"type":"Point","coordinates":[0,0]},"properties":{"name":"null spot"}},{"type":"Feature","geometry":{"type":"Point","coordinates":[1,1]},"properties":{"name": "some spot"}}]}',
                'expected' => [
                    'type'       => 'FeatureCollection',
                    'value'      => [
                        [
                            'type'       => 'Feature',
                            'geometry'   => [
                                'type'        => 'Point',
                                'coordinates' => [0, 0]
                            ],
                            'properties' => [
                                'name' => 'null spot'
                            ]
                        ],
                        [
                            'type'       => 'Feature',
                            'geometry'   => [
                                'type'        => 'Point',
                                'coordinates' => [1, 1]
                            ],
                            'properties' => [
                                'name' => 'some spot'
                            ]
                        ]
                    ],
                    'srid'       => null,
                    'dimension'  => null,
                    'properties' => []
                ]
            ],
        ];
    }
}
