<?php

require_once(__DIR__ . '/ConfigEnv.php');
$hubleto = new \HubletoMain\Loader($config);
require_once($hubleto->releaseFolder . '/hubleto');
