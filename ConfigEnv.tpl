<?php

define('GTP', "");

// urls
$config['rewriteBase']         = "/github/ceremonycrm/";
$config["url"]                 = "http://localhost/github/ceremonycrm";

// urls

$config['dir']                           = __DIR__;
$config['srcDir']                        = __DIR__ . '/app/bin';
$config['logDir']                        = __DIR__ . '/log';
$config['tmpDir']                        = __DIR__ . '/tmp';
$config['twigRootDir']                   = __DIR__ . '/app/bin';
$config['uploadDir']                     = __DIR__ . '/upload';
$config['uploadUrl']                     = '//' . ($_SERVER['HTTP_HOST'] ?? '') . $config['rewriteBase'] . 'upload';

// db
$config["db_host"]             = "localhost";
$config["db_user"]             = "root";
$config["db_password"]         = "";
$config["db_name"]             = "ceremonycrm";
$config["db_codepage"]         = "utf8mb4";
$config["global_table_prefix"] = "";

$config['db']['provider']      = "MySQLi";
$config['db']['dsn']           = "";

// misc
$config['develMode']           = TRUE;
$config['language']            = "en";

// defines
define('LOCALE_CURRENCY', '€');
define('LNG_UNIT_HOUR', 'h');
define('PHP_HOST_OS', 'Windows');
define('PHP_EXECUTABLE', 'q:\\workspace\\server\\php\\8.3.7-nts\\php.exe');
