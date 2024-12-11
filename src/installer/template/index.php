<?php

// load application class
require(__DIR__ . "/LoadApp.php");

// render
$app = new CeremonyCrmApp($config);
echo $app->render();
