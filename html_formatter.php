#!/usr/bin/env php
<?php

if (2 !== $argc) {
    echo "Missing html input\n";
    exit(1);
}

$input = $argv[1];
$doc = new DomDocument('1.0');
$doc->loadHTML($input);
$doc->formatOutput = true;

echo $doc->saveHTML() . "\n";
