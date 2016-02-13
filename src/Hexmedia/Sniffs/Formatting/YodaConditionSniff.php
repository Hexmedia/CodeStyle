<?php

/**
 * @author    Krystian Kuczek <krystian@hexmedia.pl>
 * @copyright 2013-2016 Hexmedia.pl
 */
class Hexmedia_Sniffs_Formatting_YodaConditionSniff implements PHP_CodeSniffer_Sniff
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
        return PHP_CodeSniffer_Tokens::$comparisonTokens;
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

        $operator = $tokens[$stackPtr];

        $arg1 = $this->findFirstArgument($phpcsFile, $stackPtr);

        if (null === $arg1) {
            return null;
        }

        $arg2 = $this->findSecondArgument($phpcsFile, $stackPtr);

        if (null !== $arg1) {
            if (false === $arg2[2]) {
                $phpcsFile->addWarning(
                    sprintf(
                        "Not yoda condition type. %s %s %s",
                        $tokens[$arg1[1]]['content'],
                        $operator['content'],
                        $this->getArg2Content($phpcsFile, $arg2)
                    ),
                    $stackPtr,
                    'YodaCondition',
                    array()
                );
//            } else {
//                $phpcsFile->addWarning(
//                    sprintf(
//                        "Yoda. Why not? %s %s %s",
//                        $tokens[$arg1[1]]['content'],
//                        $operator['content'],
//                        $this->getArg2Content($phpcsFile, $arg2)
//                    ),
//                    $stackPtr
//                );
            }
        }

    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int                  $stackPtr
     *
     * @return null
     */
    private function findFirstArgument(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $current = $stackPtr;

        $allowed = array(T_VARIABLE, T_WHITESPACE);

        $variable = null;
        while (--$current &&
            !in_array(
                $tokens[$current]['code'],
                array(T_OPEN_PARENTHESIS, T_BOOLEAN_AND, T_BOOLEAN_OR, T_BOOLEAN_NOT, T_RETURN)
            )
        ) {
            if (!in_array($tokens[$current]['code'], $allowed)) {
//                var_dump($tokens[$current]['type'], $tokens[$current]['content']);

                return null;
            }

            if ($tokens[$current]['code'] == T_VARIABLE) {
                if (null !== $variable) {
                    return null;
                }

                $variable = $current;
            }
        }

        return array($current, $variable);
    }

    /**
     * @param PHP_CodeSniffer_File $phpcsFile
     * @param int                  $stackPtr
     *
     * @return array
     */
    private function findSecondArgument(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $current = $stackPtr;

        while ($tokens[++$current]['code'] == T_WHITESPACE) {
        }

        $start = $current;

        $allowed = array(T_VARIABLE, T_WHITESPACE);

        $onlyVariable = true;
        for ($level = 1; isset($tokens[$current]) && !in_array(
            $tokens[$current]['code'],
            array(T_BOOLEAN_OR, T_BOOLEAN_AND, T_INLINE_THEN, T_SEMICOLON)
        ); $current++) {
            if (!in_array($tokens[$current]['code'], $allowed)) {
                $onlyVariable = false;
            }

            if ($tokens[$current]['code'] == T_OPEN_PARENTHESIS) {
                $level++;
            }

            if ($tokens[$current]['code'] == T_CLOSE_PARENTHESIS) {
                $level--;
            }

            if (0 === $level) {
                break;
            }
        }

        $end = $current;

        return array($start, $end, $onlyVariable);
    }

    private function getArg2Content(PHP_CodeSniffer_File $phpcsFile, $arg2)
    {
        $tokens = $phpcsFile->getTokens();
        $content = "";

        $start = $arg2[0];
        $end = $arg2[1];

        while ($start != $end) {

            $content .= $tokens[$start]['content'];

            $start++;
        }

        return $content;
    }
}
