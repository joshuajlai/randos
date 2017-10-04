<?php

/**
 * Simple script to calculate the number of days from the input date. Used for creating
 * S3 lifecycle policies for a specific date in the past.
 */

date_default_timezone_set('America/Los_Angeles');
$usage = "Usage: php days_til_now.php <date string>";
if ($argc < 2) {
    echo $usage . "\n";
    exit(1);
}

$inputDate = $argv[1];
printf("Input: %s\n", $inputDate);
$unixTimestamp = strtotime($inputDate);
if (false === $unixTimestamp) {
    echo "Error parsing input date\n";
    exit(1);
}

$secondsInDay = 60*60*24;
$now = time();
$diff = $now - $unixTimestamp;
printf("Difference in seconds: %s\n", $diff);
printf("Difference in days: %s\n", floor($diff / $secondsInDay));
