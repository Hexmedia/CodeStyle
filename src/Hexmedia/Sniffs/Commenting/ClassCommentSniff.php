<?php


if (class_exists('PHP_CodeSniffer_Tokenizers_Comment', true) === false) {
    $error = 'Class PHP_CodeSniffer_Tokenizers_Comment not found';
    throw new PHP_CodeSniffer_Exception($error);
}

if (class_exists('PEAR_Sniffs_Commenting_ClassCommentSniff', true) === false) {
    $error = 'Class PEAR_Sniffs_Commenting_ClassCommentSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

class Hexmedia_Sniffs_Commenting_ClassCommentSniff extends Symfony2_Sniffs_Commenting_ClassCommentSniff
{
    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int $stackPtr The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->currentFile = $phpcsFile;

        $tokens = $phpcsFile->getTokens();
        $type = strtolower($tokens[$stackPtr]['content']);
        $errorData = array($type);

        $find = PHP_CodeSniffer_Tokens::$methodPrefixes;
        $find[] = T_WHITESPACE;

        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);
        if ($tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
            && $tokens[$commentEnd]['code'] !== T_COMMENT
        ) {
            $fix = $phpcsFile->addFixableError('Missing class doc comment', $stackPtr, 'Missing');

            if (true === $fix) {
                $className = $tokens[$stackPtr + 2]['content'];

                $packageName = $this->findNamespace($tokens);

                $comment =
                    sprintf(
                        "/**" . $phpcsFile->eolChar .
                        " * Class %s" . $phpcsFile->eolChar .
                        ($packageName ? sprintf(" * @package %s" . $phpcsFile->eolChar, $packageName) : '') .
                        " */" . $phpcsFile->eolChar
                        , $className
                    );

//                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addContentBefore($stackPtr, $comment);
//                $phpcsFile->fixer->endChangeset();
            }

            $phpcsFile->recordMetric($stackPtr, 'Class has doc comment', 'no');
            return;
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Class has doc comment', 'yes');
        }

        // Try and determine if this is a file comment instead of a class comment.
        // We assume that if this is the first comment after the open PHP tag, then
        // it is most likely a file comment instead of a class comment.
        if ($tokens[$commentEnd]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
            $start = ($tokens[$commentEnd]['comment_opener'] - 1);
        } else {
            $start = $phpcsFile->findPrevious(T_COMMENT, ($commentEnd - 1), null, true);
        }

        $prev = $phpcsFile->findPrevious(T_WHITESPACE, $start, null, true);
        if ($tokens[$prev]['code'] === T_OPEN_TAG) {
            $prevOpen = $phpcsFile->findPrevious(T_OPEN_TAG, ($prev - 1));
            if ($prevOpen === false) {
                // This is a comment directly after the first open tag,
                // so probably a file comment.
                $phpcsFile->addError('Missing class doc comment', $stackPtr, 'Missing');
                return;
            }
        }

        if ($tokens[$commentEnd]['code'] === T_COMMENT) {
            $phpcsFile->addError('You must use "/**" style comments for a class comment', $stackPtr, 'WrongStyle');
            return;
        }

        // Check each tag.
        $this->processTags($phpcsFile, $stackPtr, $tokens[$commentEnd]['comment_opener']);

    }//end process()

    private function findNamespace($tokens)
    {
        foreach ($tokens as $namespacePtr => $token) {
            if ($token['code'] === T_NAMESPACE) {
                break;
            }
        }

        $namespacePtr += 2;
        $namespace = '';

        while ($tokens[$namespacePtr]['code'] == T_STRING || $tokens[$namespacePtr]['code'] == T_NS_SEPARATOR) {
            $namespace .= ($tokens[$namespacePtr]['content']);
            $namespacePtr++;
        }

        return $namespace;
    }
}
