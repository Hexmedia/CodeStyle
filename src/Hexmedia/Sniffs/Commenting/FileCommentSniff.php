<?php

/**
 * @copyright 2014-2016 hexmedia.pl
 * @author    Krystian Kuczek <krystian@hexmedia.pl>
 */
class Hexmedia_Sniffs_Commenting_FileCommentSniff extends PEAR_Sniffs_Commenting_FileCommentSniff
{

    /**
     * Tags in correct order and related info.
     *
     * @var array
     */
    protected $tags = array(
        '@category' => array(
            'required' => false,
            'allow_multiple' => false,
        ),
        '@package' => array(
            'required' => false,
            'allow_multiple' => false,
        ),
        '@subpackage' => array(
            'required' => false,
            'allow_multiple' => false,
        ),
        '@author' => array(
            'required' => true,
            'allow_multiple' => true,
        ),
        '@copyright' => array(
            'required' => true,
            'allow_multiple' => true,
        ),
        '@license' => array(
            'required' => true,
            'allow_multiple' => false,
        ),
        '@version' => array(
            'required' => false,
            'allow_multiple' => false,
        ),
        '@link' => array(
            'required' => false,
            'allow_multiple' => true,
        ),
        '@see' => array(
            'required' => false,
            'allow_multiple' => true,
        ),
        '@since' => array(
            'required' => false,
            'allow_multiple' => false,
        ),
        '@deprecated' => array(
            'required' => false,
            'allow_multiple' => false,
        ),
    );

    /**
     * Process the copyright tags.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param array $tags The tokens for these tags.
     *
     * @return void
     */
    protected function processCopyright(PHP_CodeSniffer_File $phpcsFile, array $tags)
    {
        parent::processCopyright($phpcsFile, $tags);

        $tokens = $phpcsFile->getTokens();
        foreach ($tags as $tag) {
            if ($tokens[($tag + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                // No content.
                continue;
            }

            $content = $tokens[($tag + 2)]['content'];
            $matches = array();
            if (preg_match('/^([0-9]{4})((.{1})([0-9]{4}))? (.+)$/', $content, $matches) !== 0) {
                if ($matches[3] !== '') {
                    if ($matches[1] !== "2013" || $matches[4] !== date("Y") || $matches[5] != "Hexmedia.pl") {
                        $fix = $phpcsFile->addFixableWarning("Not hexmedia:)", $tag, "NotHexmedia");

                        if (true === $fix) {
                            $phpcsFile->fixer->replaceToken(($tag + 2), sprintf("2013-%s Hexmedia.pl", date("Y")));
                        }
                    }
                }
            }
        }//end foreach
    }//end processCopyright()
}
