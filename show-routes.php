#!/usr/bin/env php
<?php

require 'vendor/autoload.php';

use Rodrifarias\SlimRouteAttributes\Command\ShowRoutesCommand;
use Symfony\Component\Console\Application;

try {
    $app = new Application();
    $app->add(new ShowRoutesCommand());
    $app->run();
} catch (Exception $e) {
    echo "Error running console application [{$e->getMessage()}]";
}