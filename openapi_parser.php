<?php

$usage = 'php openapi_parser.php <path_to_file> <component>';
if ($argc < 3) {
	echo $usage . "\n";
	exit(1);
}

function print_info($json_data) {
	$single_level_keys = [
		'info',
		'openapi',
		'security',
		'servers',
	];
	foreach ($single_level_keys as $key) {
		echo print_r($json_data[$key], 1) . "\n";
	}
}


$input_file = $argv[1];
$component = $argv[2];
$contents = file_get_contents($input_file);
$json_data = json_decode($contents, true);

foreach ($json_data['components']['schemas'] as $key => $value) {
	if ($component != $key) {
		continue;
	}
	echo json_encode($value, JSON_PRETTY_PRINT) . "\n";
}
