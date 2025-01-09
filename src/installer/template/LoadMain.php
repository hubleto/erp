<?php

ini_set('html_errors', "1");
ini_set('error_prepend_string', "<pre style='color: #333; font-face:monospace; font-size:8pt;'>");
ini_set('error_append_string', "</pre>");

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL ^ E_NOTICE ^ E_WARNING);

define('_ADIOS_ID', 'HubletoMain-{{ uid }}');

// load configs
require_once("{{ mainRootFolder }}/ConfigApp.php");
require_once(__DIR__ . "/ConfigAccount.php");

// load autoloaders
require("{{ mainRootFolder }}/vendor/autoload.php");

// load entry class
require("{{ mainRootFolder }}/src/Main.php");

