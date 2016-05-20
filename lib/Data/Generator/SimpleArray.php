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

namespace CrEOF\Geo\Obj\Data\Generator;

use CrEOF\Geo\Obj\Exception\RuntimeException;
use CrEOF\Geo\Obj\Exception\UnknownTypeException;
use CrEOF\Geo\Obj\Exception\UnsupportedFormatException;
use CrEOF\Geo\Obj\Object;

/**
 * Class SimpleArray
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class SimpleArray implements GeneratorInterface
{
    /**
     * @param mixed       $value
     * @param null|string $typeHint
     *
     * @return array
     * @throws RuntimeException
     * @throws UnsupportedFormatException
     * @throws UnknownTypeException
     */
    public function generate($value, $typeHint = null)
    {
        if (! is_array($value)) {
            throw new UnsupportedFormatException();
        }

        if ($this->isObject($value)) {
            $value = $this->getDimensionFromType($value);
        }

        $data = [
            'type'  => Object::getProperTypeName(array_key_exists('type', $value) ? $value['type'] : $typeHint),
            'value' => array_key_exists('value', $value) ? $value['value'] : $value,
            'srid'  => array_key_exists('srid', $value) ? $value['srid'] : null
        ];

        $data['dimension'] = array_key_exists('dimension', $value) ? $this->getDimension($value['dimension']) : null;
        $data['properties'] = array_key_exists('properties', $value) ? $value['properties'] : [];

        return $data;
    }

    /**
     * @param null|string $dimension
     *
     * @return null|string
     */
    private function getDimension($dimension)
    {
        return null !== $dimension ? strtoupper($dimension) : null;
    }

    /**
     * @param array $value
     *
     * @return array
     * @throws RuntimeException
     */
    private function getDimensionFromType(array $value)
    {
        $matches = [];

        preg_match('/(\w+)(?:\s*)(z|m|zm)$/i', $value['type'], $matches);

        if (3 === count($matches)) {
            if (array_key_exists('dimension', $value) && $matches[2] !== $value['dimension']) {
                throw new RuntimeException('Specified dimension "' . strtoupper($value['dimension']) . '" does not match type "' . $value['type'] . '"');
            }

            $value['type']      = $matches[1];
            $value['dimension'] = $matches[2];
        }

        return $value;
    }

    /**
     * @param array $value
     *
     * @return bool
     */
    private function isObject(array $value)
    {
        return array_key_exists('type', $value) && array_key_exists('value', $value) && is_array($value['value'] && $this->isSequential($value));
    }

    /**
     * @param array $array
     *
     * @return bool
     */
    private function isSequential(array $array)
    {
        return array_keys($array) === range(0, count($array) - 1);
    }
}
