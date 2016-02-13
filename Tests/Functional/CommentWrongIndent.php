<?php

/**
 * @author    Krystian Kuczek <krystian@hexmedia.pl>
 * @copyright 2013-2016 Hexmedia.pl
 * @license   @see LICENSE
 */

namespace A;

/**
 * Class FunctionDocblock
 */
class FunctionDocblock
{
    /**
     * @param string $a
     * @param int    $b
     * @param int    $c
          *
     * @return $this
     */
    public function setSomenting($a, $b, $c)
    {
        return $this;
    }

    /**
     * @param int $a
 *
     * @return $this
     */
    public function setSomethingElse($a)
    {
        return $this;
    }
}
