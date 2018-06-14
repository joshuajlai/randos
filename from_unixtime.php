#!/usr/bin/env php
<?php

$input = $argv[1];

echo date(DATE_ATOM, $input) . "\n";
