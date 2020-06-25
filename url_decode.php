#!/usr/bin/env php
<?php

$usage = "php url_decode.php <string>";
if ($argc < 2) {
    echo $usage . "\n";
    exit(1);
}

$string = $argv[1];
echo urldecode($string) . "\n";

