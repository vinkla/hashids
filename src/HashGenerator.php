<?php

/*
 * This file is part of Hashids.
 *
 * (c) Ivan Akimov <ivan@barreleye.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hashids;

/**
 * This is the hash generator interface.
 *
 * @author Ivan Akimov <ivan@barreleye.com>
 */
interface HashGenerator
{
    /**
     * Encodes a variable number of parameters to generate a hash.
     *
     * @param mixed ...
     *
     * @return string the generated hash
     */
    public function encode();

    /**
     * Decodes a hash to the original parameter values.
     *
     * @param string $hash the hash to decode
     *
     * @return array
     */
    public function decode($hash);

    /**
     * Encodes hexadecimal values to generate a hash.
     *
     * @param string $str hexadecimal string
     *
     * @return string the generated hash
     */
    public function encode_hex($str);

    /**
     * Decodes hexadecimal hash.
     *
     * @param string $hash
     *
     * @return string hexadecimal string
     */
    public function decode_hex($hash);
}
