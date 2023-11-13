<?php

$usage = "php generate_strings.php <count>";
if ($argc < 2) {
  echo $usage;
  exit(1);
}
$count = $argv[1];
for ($x = 0; $x < $count; $x++) {
  echo substr(md5(microtime()),rand(0,26),5) . "\n";
}
