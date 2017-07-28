<?php
$usage = "Usage: php print_ephemeral_mounts.php <number, 24 or less\n";
if ($argc < 2) {
    echo $usage;
    exit(1);
}

$count = $argv[1];
if ($count > 24) {
    echo $usage;
    exit(1);
}

$text = "
ephemeral_block_device {
    device_name = \"%s\"
    virtual_name = \"%s\"
}
";

$alphabet = array(
    "a",
    "b",
    "c",
    "d",
    "e",
    "f",
    "g",
    "h",
    "i",
    "j",
    "k",
    "l",
    "m",
    "o",
    "p",
    "q",
    "r",
    "s",
    "t",
    "u",
    "v",
    "w",
    "x",
    "y",
    "z"
);
$baseName = "/dev/xvd";
for ($i = 0; $i < $count; $i++) {
    if ($i == 0) {
        printf($text, $baseName . "b", "ephemeral" . $i);
        continue;
    } else if ($i == 1) {
        printf($text, $baseName . "c", "ephemeral" . $i);
        continue;
    } else {
        printf($text, $baseName . "b" . $alphabet[$i - 2], "ephemeral" . $i);
    }
}
