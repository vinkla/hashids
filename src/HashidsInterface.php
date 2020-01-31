<?php

/**
 * Copyright (c) Ivan Akimov.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/vinkla/hashids
 */

namespace Hashids;

interface HashidsInterface
{
    /**
     * Encode parameters to generate a hash.
     *
     * @param mixed $numbers
     *
     * @return string
     */
    public function encode(...$numbers);

    /**
     * Decode a hash to the original parameter values.
     *
     * @param string $hash
     *
     * @return array
     */
    public function decode($hash);

    /**
     * Encode hexadecimal values and generate a hash string.
     *
     * @param string $str
     *
     * @return string
     */
    public function encodeHex($str);

    /**
     * Decode a hexadecimal hash.
     *
     * @param string $hash
     *
     * @return string
     */
    public function decodeHex($hash);
}
