<?php

// bootstrap
require_once(__DIR__ . "/boot.php");

// run cron
$hubleto->cronManager()->init();
$hubleto->cronManager()->run();
