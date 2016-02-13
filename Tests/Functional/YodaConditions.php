<?php
/**
 * @author    Krystian Kuczek <krystian@hexmedia.pl>
 * @copyright 2013-2016 Hexmedia.pl
 * @license   @see LICENSE
 */

namespace Name\Space;

/**
 * Class YodaStyleFix
 *
 * @package Name\Space
 */
class YodaConditions
{
    /**
     * @return bool
     */
    public function ifElseString()
    {
        $b = 1;

        if ($b == "a") {
            return true;
        } elseif ($b != "a" . "b") {
            return true;
        } elseif ($b === "a" + ("$b" . "b")) { //NEED extra support for ()
            return true;
        }

        $c = 1;

        if ($b == $c &&
            $c == $b
        ) {

        }
    }

    /**
     * @return bool
     */
    public function inlineString()
    {
        $c = 1;

        return $c >= "a";
    }

    /**
     * @return bool
     */
    public function ifElseBool()
    {
        $b = 1;

        if ($b < true) {
            return true;
        } elseif ($b <= false) {
            return true;
        } elseif ($b > true) {
            return true;
        }

        if ($b + 2 > true) {
            return false;
        }

        $e = 2;
        if ($b + 2 > true &&
            $e == 2 && //Why this is good?
            2 + $e == 2
        ) {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function inlineBool()
    {
        $c = 1;
        $g = 2;

        $d = ($g == $c ? "Yes" : "no");

        return $c === true;
    }
}
