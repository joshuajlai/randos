<?php

$usage = "php get_csv_column.php <input_file>";
if ($argc < 2) {
    echo $usage;
}
$input = $argv[1];
$file_pointer = fopen($input, 'r');
$column_number = 18;

while ($row = fgetcsv($file_pointer)) {
    print($row[$column_number] . "\n");
}
fclose($file_pointer);

