<?php

$usage = "php mysql_name_toolong.php <name>";
if ($argc < 2) {
    echo $usage . "\n";
    exit(1);
}

$name = $argv[1];
if (strlen($name) <= 16) {
    echo "${name} is good\n";
} else {
    $shortName = substr($name, 0, 16);
    echo "${name} is too long\n";
    echo "Try: ${shortName}\n";
}
