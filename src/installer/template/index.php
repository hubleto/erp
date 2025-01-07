<?php

// load application class
require(__DIR__ . "/LoadMain.php");

// render
$app = new HubletoMain($config);
echo $app->render();
