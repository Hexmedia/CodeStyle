<?php
/**
 * @author    Krystian Kuczek <krystian@hexmedia.pl>
 * @copyright 2013-2016 Hexmedia.pl
 * @license   @see LICENSE
 */

namespace Hexmedia\CodeStyle\Composer;

use Symfony\Component\Filesystem\Filesystem;
use Composer\Script\Event;

/**
 * Class InstallPhpcs
 *
 * @package Hexmedia\CodeStyle\Composer
 */
class PhpCSInstall
{
    /**
     * @param Event $event
     */
    public static function postAutoloadDump(Event $event)
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');

        $fileSystem = new Filesystem();

        $source = $vendorDir . "/escapestudios/symfony2-coding-standard/Symfony2";
        $destination = $vendorDir . "/squizlabs/php_codesniffer/CodeSniffer/Standards/Symfony2";

        if ($fileSystem->exists($destination)) {
            $fileSystem->remove($destination);
        }

        $fileSystem->mirror(
            $source,
            $destination,
            null,
            array('copy_on_windows' => PHP_OS === "WinNT", 'override' => true)
        );
    }
}
