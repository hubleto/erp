<?php

// load application class
require(__DIR__ . "/LoadApp.php");

// render
$app = new HubletoMain($config);
echo $app->render();
