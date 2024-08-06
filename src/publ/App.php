<?php

use \ADIOS\Core\Helper;

// load configs
require_once(__DIR__ . "/ConfigApp.php");

// include autoloaders
require_once(__DIR__ . "/../../vendor/autoload.php");


// autoloader pre CeremonyCrmApp
spl_autoload_register(function($className) {
  if (strpos($className, 'CeremonyCrmApp\\') === 0) {
    $className = str_replace('CeremonyCrmApp\\', '', $className);

    $fname = dirname(__FILE__) . '/' . str_replace('\\', '/', $className) . '.php';

    if (is_file($fname)) require_once($fname);
  }
});

// create own ADIOS class
class CeremonyCrmApp extends \ADIOS\Core\Loader {
  public function __construct($config = NULL, $mode = NULL) {
    parent::__construct($config, $mode);

    if ($mode == self::ADIOS_MODE_FULL) {
      $this->twig->addFunction(new \Twig\TwigFunction(
        'number',
        function ($amount) { return number_format($amount, 2, ",", " "); }
      ));
    }
  }
}
