<?php

use App\Service\DatabaseService;
use App\Service\DiscordService;
use App\Service\RiotApi;

require_once(__DIR__ . "/vendor/autoload.php");

$ds = new DatabaseService;

// $s = $api->getSummoner("Slappez");
// var_dump($s);
// exit;