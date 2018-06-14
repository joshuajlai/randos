#!/usr/bin/env php
<?php

$input = $argv[1];

$components = explode('.', $input);
foreach ($components as $component) {
    echo base64_decode($component) . "\n";
}
