#!/bin/bash

if [ -d vendor ]; then
    vendor="vendor/"
fi

cp -r "$vendor/escapestudios/symfony2-coding-standard/Symfony2" "$vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/"
