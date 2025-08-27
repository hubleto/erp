<?php

require_once(__DIR__ . '/ConfigEnv.php');
$hubleto = new \Hubleto\Erp\Loader($config);
require_once($hubleto->releaseFolder . '/hubleto');
