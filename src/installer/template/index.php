<?php

// load application class
require(__DIR__ . "/LoadMain.php");

// render
$main = new HubletoMain($config);
echo $main->render();
