<?php

// bootstrap
require_once(__DIR__ . "/boot.php");

// run cron
$main->getCronManager()->init();
$main->getCronManager()->run();
