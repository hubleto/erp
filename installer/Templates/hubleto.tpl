<?php

require_once(__DIR__ . '/ConfigEnv.php');
$tmpMain = new \HubletoMain\Loader($config);
require_once($tmpMain->releaseFolder . '/hubleto');
