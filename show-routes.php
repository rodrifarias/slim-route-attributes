#!/usr/bin/env php
<?php

require 'vendor/autoload.php';

use Rodrifarias\SlimRouteAttributes\Command\ShowRoutesCommand;
use Rodrifarias\SlimRouteAttributes\Route\Scan\ScanRoutes;
use Symfony\Component\Console\Application;

try {
    $scan = new ScanRoutes();
    $command = new ShowRoutesCommand($scan);

    $app = new Application();
    $app->add($command);
    $app->run();
} catch (Exception $e) {
    echo "Error running console application [{$e->getMessage()}]";
}