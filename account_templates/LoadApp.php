<?php

ini_set('html_errors', "1");
ini_set('error_prepend_string', "<pre style='color: #333; font-face:monospace; font-size:8pt;'>");
ini_set('error_append_string', "</pre>");

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE ^ E_WARNING);

define('_ADIOS_ID', 'CeremonyCrmApp-{{ uid }}');

// load configs
require_once("{{ appDir }}/src/core/ConfigApp.php");
require_once("{{ appDir }}/ConfigEnv.php");
require_once(__DIR__ . "/ConfigAccount.php");

// load autoloaders
require("{{ appDir }}/vendor/autoload.php");
require("{{ extDir }}/vendor/autoload.php");

// load application class
require("{{ appDir }}/src/core/App.php");

