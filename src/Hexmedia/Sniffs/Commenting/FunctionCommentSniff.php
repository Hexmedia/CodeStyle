<?php

/**
 * @author    Krystian Kuczek <krystian@hexmedia.pl>
 * @copyright 2013-2016 Hexmedia.pl
 */
class  Hexmedia_Sniffs_Commenting_FunctionCommentSniff extends Symfony2_Sniffs_Commenting_FunctionCommentSniff
{
    /**
     * Process the return comment of this function comment.
     *
     * @param PHP_CodeSniffer_File $phpcsFile    The file being scanned.
     * @param int                  $stackPtr     The position of the current token
     *                                           in the stack passed in $tokens.
     * @param int                  $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    protected function processReturn(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $commentStart)
    {
        parent::processReturn($phpcsFile, $stackPtr, $commentStart);

        $tokens = $phpcsFile->getTokens();

        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if ($tokens[$tag]['content'] === '@return') {
                $line = $tokens[$tag]['line'];

                //Previous line should be empty

                $tagPrevLine = $tag;

                while ($tokens[--$tagPrevLine]['line'] != $line - 1) {
                    ;
                }

                $prevLine = $tagPrevLine++;

                while ($tokens[--$tagPrevLine]['line'] != $line - 2) {
                    ;
                }

                $tagPrevLine++;

                for (; $tokens[$tagPrevLine]['line'] != $tokens[$tag]['line']; $tagPrevLine++) {
                    if ($tokens[$tagPrevLine]['code'] !== T_DOC_COMMENT_WHITESPACE
                        && $tokens[$tagPrevLine]['code'] != T_DOC_COMMENT_STAR
                        && $tokens[$tagPrevLine]['code'] != T_DOC_COMMENT_OPEN_TAG
                        && $tokens[$tagPrevLine]['code'] != T_WHITESPACE
                    ) {
                        $fix = $phpcsFile
                            ->addFixableError("No empty docline before @return", $tag, "NoNewLineBeforeReturn");

                        if (true === $fix) {
                            $phpcsFile->fixer->addContent($prevLine, " *" . $phpcsFile->eolChar);
                        }

                        break;
//                            var_dump($phpcsFile->fixer->fixFile());
//                            die();
//                        }
                    }
                }
            }
        }
    } /* end processReturn() */
}
