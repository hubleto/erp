<?php

require('boot.php');
echo $main->render();

// run
$main->crons->init();
$main->crons->run();
