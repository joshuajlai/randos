#!/usr/bin/env php
<?php

if (2 !== $argc) {
    echo "Need input file\n";
    exit(1);
}

$input = file_get_contents($argv[1]);
echo json_encode(json_decode($input, true), JSON_PRETTY_PRINT) . "\n";
