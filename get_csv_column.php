<?php

$usage = "php get_csv_column.php <input_file> <column_number> <skip_header>";
if ($argc < 4) {
    echo $usage;
    exit();
}
$input = $argv[1];
$column_number = $argv[2];
$skip_header = (bool) $argv[3];
$file_pointer = fopen($input, 'r');

if ($skip_header) {
    fgetcsv($file_pointer);
}

while ($row = fgetcsv($file_pointer)) {
    print($row[$column_number] . "\n");
}
fclose($file_pointer);

