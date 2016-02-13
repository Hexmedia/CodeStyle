<?php
/**
 * @author    Krystian Kuczek <krystian@hexmedia.pl>
 * @copyright 2014-2016 hexmedia.pl
 */

namespace Name\Space;

class IfElseIf
{
    public function __construct()
    {
        if ("a" == "b") {
            return true;
        } else if ("a" == "c") {
            return false;
        }
    }
}
