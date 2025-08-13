<?php

// bootstrap
require_once(__DIR__ . "/boot.php");

// run cron
$main->crons->init();
$main->crons->run();
