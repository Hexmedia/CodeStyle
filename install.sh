#!/bin/bash

vendor=""

if [ -d "./vendor" ]; then
    vendor="vendor/"
fi

echo "`pwd`"

if [ "$vendor" != "" ]; then
    echo "Installing Symfony2."
    cp -r "$vendor/escapestudios/symfony2-coding-standard/Symfony2" "$vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/"
fi
