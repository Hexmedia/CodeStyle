<?php

/**
 * @author    Krystian Kuczek <krystian@hexmedia.pl>
 * @copyright 2013-2016 Hexmedia.pl
 */
class Hexmedia_Sniffs_Formatting_DocblockSameIndentSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * An example return value for a sniff that wants to listen for whitespace
     * and any comments would be:
     *
     * <code>
     *    return array(
     *            T_WHITESPACE,
     *            T_DOC_COMMENT,
     *            T_COMMENT,
     *           );
     * </code>
     *
     * @return int[]
     * @see    Tokens.php
     */
    public function register()
    {
        return array(
//            T_DOC_COMMENT_WHITESPACE,
//            T_DOC_COMMENT_STAR,
//            T_DOC_COMMENT,
//            T_DOC_COMMENT_CLOSE_TAG,
            T_DOC_COMMENT_OPEN_TAG,
//            T_DOC_COMMENT_STRING,
//            T_DOC_COMMENT_TAG,
        );
    }

    /**
     * Called when one of the token types that this sniff is listening for
     * is found.
     *
     * The stackPtr variable indicates where in the stack the token was found.
     * A sniff can acquire information this token, along with all the other
     * tokens within the stack by first acquiring the token stack:
     *
     * <code>
     *    $tokens = $phpcsFile->getTokens();
     *    echo 'Encountered a '.$tokens[$stackPtr]['type'].' token';
     *    echo 'token information: ';
     *    print_r($tokens[$stackPtr]);
     * </code>
     *
     * If the sniff discovers an anomaly in the code, they can raise an error
     * by calling addError() on the PHP_CodeSniffer_File object, specifying an error
     * message and the position of the offending token:
     *
     * <code>
     *    $phpcsFile->addError('Encountered an error', $stackPtr);
     * </code>
     *
     * @param PHP_CodeSniffer_File $phpcsFile The PHP_CodeSniffer file where the
     *                                        token was found.
     * @param int                  $stackPtr  The position in the PHP_CodeSniffer
     *                                        file's token stack where the token
     *                                        was found.
     *
     * @return void|int Optionally returns a stack pointer. The sniff will not be
     *                  called again on the current file until the returned stack
     *                  pointer is reached. Return (count($tokens) + 1) to skip
     *                  the rest of the file.
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $column = $tokens[$stackPtr]['column'] + 1;

        $current = $stackPtr;
        for (; $tokens[$current]['code'] != T_DOC_COMMENT_CLOSE_TAG; $current++) {
            if ($tokens[$current]['code'] == T_DOC_COMMENT_STAR) {
                if ($tokens[$current]['column'] != $column) {
                    $fix = $phpcsFile->addFixableError(
                        sprintf(
                            "Doc comment has %d spaces intend, should have %d",
                            $tokens[$current]['column'],
                            $column
                        ),
                        $stackPtr,
                        "WrongIndentInDocComment"
                    );


                    if (true === $fix) {
                        $spaces = "";
                        $toAdd = $column - 1;

                        while ($toAdd-- > 0) {
                            $spaces .= " ";
                        }

                        $phpcsFile->fixer->replaceToken($current - 1, $spaces);
                    }
                }
            }
        }
    }
}
