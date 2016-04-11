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

namespace CrEOF\Geo\Obj\Value\Generator;

use CrEOF\Geo\Obj\Exception\UnsupportedFormatException;
use CrEOF\Geo\Obj\ObjectInterface;
use CrEOF\Geo\String\Parser;

/**
 * Class GeoString
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class GeoString implements ValueGeneratorInterface
{
    /**
     * @var Parser
     */
    private static $parser;

    /**
     * Wkb constructor
     */
    public function __construct()
    {
        if (null === self::$parser) {
            self::$parser = new Parser();
        }
    }

    /**
     * @param mixed           $value
     * @param ObjectInterface $object
     *
     * @return array
     * @throws UnsupportedFormatException
     */
    public function generate($value, ObjectInterface $object)
    {
        if (! is_string($value) && ! is_numeric($value[0])) {
            throw new UnsupportedFormatException();
        }

        return [
            'value'     => self::$parser->parse($value),
            'type'      => 'point',
            'srid'      => null,
            'dimension' => null,
        ];
    }
}
