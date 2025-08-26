<?php

// bootstrap
require_once(__DIR__ . "/boot.php");

// run cron
$hubleto->getCronManager()->init();
$hubleto->getCronManager()->run();
