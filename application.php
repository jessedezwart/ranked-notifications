#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use App\Command\AddUserCommand;
use App\Command\RunCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Dotenv;

$application = new Application();

// Load environment variables
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . "/.env");

// Add commands
$application->add(new AddUserCommand());
$application->add(new RunCommand());

$application->run();