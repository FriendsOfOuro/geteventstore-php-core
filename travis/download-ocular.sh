#!/bin/bash

file="ocular.phar"

if [ ! -f "$HOME/downloads/$file" ]; then
    echo "File $file not cached, downloading..."
    wget "https://scrutinizer-ci.com/$file" -O "$HOME/downloads/$file"
else
    echo "File $file found in cache"
    cp "$HOME/downloads/$file" ~
fi
