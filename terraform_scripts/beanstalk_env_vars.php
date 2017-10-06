<?php
/**
 * Expected json format is normal key-value pairs:
 * {
 *   "key": "value",
 *   "key2": "value2",
 *   "some_key": "${var.some_tf_var}"
 * }
 */
$usage = "Usage: php beanstalk_env_vars.php <json_file.json>\n";
if ($argc < 2) {
    echo $usage;
    exit(1);
}

$jsonFile = $argv[1];
if (! file_exists($jsonFile)) {
    printf("Cannot reach file %s", $jsonFile);
    exit(1);
}

$text = '
  setting {
    namespace = "aws:elasticbeanstalk:application:environment"
    name      = "{{name}}"
    value     = "{{value}}"
  }
';
$contents = file_get_contents($jsonFile);
$jsonData = json_decode($contents, true);
if (false === $jsonData) {
    printf("Invalid json data: %s", json_last_error_msg());
    exit(1);
}

print_r($jsonData);

$result = [];
foreach ($jsonData as $key => $value) {
    // why doesn't php have named arguments yet =(
    $block = $text;
    $block = str_replace("{{name}}", $key, $block);
    $block = str_replace("{{value}}", $value, $block);
    $result[] = $block;
}

printf(implode("", $result));
// todo: consider writing this in python
