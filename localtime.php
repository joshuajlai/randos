<?php

$input = $argv[1];
date_default_timezone_set("America/Los_Angeles");
echo date(DATE_ATOM, strtotime($input)) . "\n";
