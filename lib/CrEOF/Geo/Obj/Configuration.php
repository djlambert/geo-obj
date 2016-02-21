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

namespace CrEOF\Geo\Obj;

use CrEOF\Geo\Obj\Exception\UnsupportedTypeException;
use CrEOF\Geo\Obj\Validator\ValidatorInterface;

/**
 * Class Configuration
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
final class Configuration
{
    /**
     * @var Configuration
     */
    private static $configuration;

    /**
     * @var ValidatorInterface[]
     */
    private $validators;

    private function __construct()
    {
        $this->validators = array();
    }

    /**
     * @return Configuration
     */
    public static function getConfiguration()
    {
        if (null === self::$configuration) {
            self::$configuration = new self();
        }

        return self::$configuration;
    }

    /**
     * @param string             $type
     * @param ValidatorInterface $validator
     *
     * @throws UnsupportedTypeException
     */
    public function setValidator($type, ValidatorInterface $validator)
    {
        $this->validateObjectType($type);

        $this->validators[$type] = $validator;
    }

    /**
     * @param string $type
     *
     * @return ValidatorInterface
     *
     * @throws UnsupportedTypeException
     */
    public function getValidator($type)
    {
        $this->validateObjectType($type);

        return array_key_exists($type, $this->validators) ?  $this->validators[$type] : null;
    }

    /**
     * @param string $type
     *
     * @throws UnsupportedTypeException
     */
    private function validateObjectType($type)
    {
        if (! class_exists($type) || ! is_subclass_of($type, 'CrEOF\Geo\Obj\ObjectInterface')) {
            throw new UnsupportedTypeException('Unsupported type "' . $type . '"');
        }
    }
}
