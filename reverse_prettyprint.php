#!/usr/bin/env php
<?php

if (2 !== $argc) {
    echo "Need input file\n";
    exit(1);
}

$input = file_get_contents($argv[1]);
$decoded = json_decode($input, true);
if (false == $decoded) {
    echo json_last_error_msg(). "\n";
    exit;
}
echo json_encode($decoded, JSON_UNESCAPED_SLASHES) . "\n";
