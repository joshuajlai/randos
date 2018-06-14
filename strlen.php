#!/usr/bin/env php
<?php
$usage = "strlen.php <input_string>";
if ($argc < 2) {
    echo $usage . "\n";
    exit(1);
}

$input = $argv[1];
echo strlen($input) . "\n";
