<?php

// load application class
require(__DIR__ . "/LoadApp.php");

// render
$app = new HubletoCore($config);
echo $app->render();
