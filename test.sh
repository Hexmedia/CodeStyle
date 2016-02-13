#!/bin/bash

function clearMatch() {
    found=`find ./Tests/Functional -path "$1"`

    if [ "$found" != "" ] ; then
        for file in $found
        do
            rm $file
        done;
    fi
}

function clearNow() {
    clearMatch "*.fixed.php"
    clearMatch "*.diff.new"
}

#before cleanup
clearNow

./vendor/bin/phpcs --standard=./src/Hexmedia/ ./Tests/Functional/ --extensions=php
./vendor/bin/phpcbf --standard=./src/Hexmedia/ ./Tests/Functional --suffix=.fixed.php --exclude=*.diff

for fixed in `find ./Tests/Functional -path "*.fixed.php"`
do
    original=${fixed/.fixed.php/}
    diff=${original/.php/.diff}
    echo -e "\e[32mComparing \e[33m$original \e[32m to \e[33m$fixed\e[0m"

    diff $original $fixed > "$diff.new"

    if [ "`diff ""$diff "$diff.new"`" != "" ] ; then
        echo -e "\e[35mWrong diffrence between fixed and expected.\e[0m"
        exit 1;
    fi
done

#after cleanup
clearNow
