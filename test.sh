#!/bin/bash

verbose=0

#TODO: change it to some more clever way
if [ "$1" == "-v" ]; then
    verbose=1
fi

file_to_check=""
if [ -f "./Tests/Functional/$1" ]; then
    file_to_check=$1

    if [ "$2" == "-v" ]; then
        verbose=1
    fi
fi

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

function exitIfError() {
    if [ "$1" != "0" ] && [ "$1" != "1" ]; then
        exit $1
    fi
}

#before cleanup
clearNow

./vendor/bin/phpcs --standard=./src/Hexmedia/ "./Tests/Functional/$file_to_check" --extensions=php
exitIfError $?
./vendor/bin/phpcbf --standard=./src/Hexmedia/ "./Tests/Functional/$file_to_check" --suffix=.fixed.php --exclude=*.diff
exitIfError $?

for fixed in `find ./Tests/Functional -path "*.fixed.php"`
do
    original=${fixed/.fixed.php/}
    diff=${original/.php/.diff}
    echo -e "\e[32mComparing \e[33m$original \e[32m to \e[33m$fixed\e[0m"

    diff $original $fixed > "$diff.new"

    diffrenece="`diff ""$diff "$diff.new"`"

    if [ "$diffrenece" != "" ] ; then
        echo -e "\e[35mWrong diffrence between fixed and expected in $original.\e[0m"

        if [ $verbose -gt 0 ]; then
            echo -e "\e[32mDiffrence is:\e[0m"
            echo -e "\e[33m$diffrenece\e[0m"
        fi

        exit 1;
    fi
done

#after cleanup
clearNow
