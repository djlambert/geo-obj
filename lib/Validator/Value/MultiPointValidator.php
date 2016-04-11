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

namespace CrEOF\Geo\Obj\Validator\Value;

use CrEOF\Geo\Obj\Configuration;
use CrEOF\Geo\Obj\Exception\ExceptionInterface;
use CrEOF\Geo\Obj\Exception\RangeException;
use CrEOF\Geo\Obj\Exception\UnexpectedValueException;
use CrEOF\Geo\Obj\Object;
use CrEOF\Geo\Obj\Validator\AbstractValidator;

/**
 * Class MultiPointValidator
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class MultiPointValidator extends AbstractValidator
{
    /**
     * MultiPointValidator constructor
     */
    public function __construct()
    {
        $this->setExpectedType(Object::T_MULTIPOINT);
    }

    /**
     * @param array $value
     *
     * @throws ExceptionInterface
     */
    public function validate(array &$value)
    {
        parent::validate($value);

        foreach ($value['value'] as $point) {
            $this->validatePoint($point);
        }
    }

    /**
     * @param mixed $point
     *
     * @throws ExceptionInterface
     */
    protected function validatePoint($point)
    {
        if (! is_array($point)) {
            throw new UnexpectedValueException('MultiPoint value must be array of "array", "' . gettype($point) . '" found');
        }

        $point = [
            'type' => 'point',
            'value' => $point
        ];

        try {
            Configuration::getInstance()->getValidators(Object::T_POINT)->validate($point);
        } catch (ExceptionInterface $e) {
            throw new RangeException('Bad point value in MultiPoint. ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
