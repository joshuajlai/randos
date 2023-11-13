#!/usr/bin/env php
<?php
$usage = "strlen.php <input_string>";
if ($argc < 2) {
    $input = stream_get_contents(STDIN);
} else {
    $input = $argv[1];
}
if (! strlen($input)) {
    echo $usage . "\n";
    exit(1);
}
echo strlen($input) . "\n";
