<?php
/**
 * @author    Krystian Kuczek <krystian@hexmedia.pl>
 * @copyright 2014-2016 Hexmedia.pl
 * @license   @see LICENSE
 */

namespace Name\Space;

/**
 * Class SomeClass
 * @package Name\Space
 */
class SomeClass
{
    /**
     * SomeClass constructor.
     */
    public function __construct() {
        $this->a = "a"."c";
        $this->n = "a" . "c";
    }
}
