<?php

$usage = "php url_encode.php <string>";
if ($argc < 2) {
    echo $usage . "\n";
    exit(1);
}

$string = $argv[1];
echo urlencode($string) . "\n";

