<?php

use \ADIOS\Core\Helper;

// load configs
require_once(__DIR__ . "/ConfigApp.php");

// include autoloaders
require_once(__DIR__ . "/../../vendor/autoload.php");


// autoloader pre CeremonyCrmApp
spl_autoload_register(function($class) {
  $class = str_replace('\\', '/', $class);
  if (strpos($class, 'CeremonyCrmApp/') === 0) {
    require_once(__DIR__ . '/' . str_replace('CeremonyCrmApp/', '', $class) . '.php');
  }
});

// create own ADIOS class
class CeremonyCrmApp extends \ADIOS\Core\Loader {
  protected array $registeredModules = [];
  protected \CeremonyCrmApp\Core\Sidebar $sidebar;

  public function __construct($config = NULL, $mode = NULL) {
    parent::__construct($config, $mode);

    if ($mode == self::ADIOS_MODE_FULL) {
      $this->twig->addFunction(new \Twig\TwigFunction(
        'number',
        function ($amount) { return number_format($amount, 2, ",", " "); }
      ));
    }

    $this->registerModule(\CeremonyCrmApp\Modules\Core\Settings\Loader::class);
    $this->registerModule(\CeremonyCrmApp\Modules\Core\Customers\Loader::class);

    $this->sidebar = new \CeremonyCrmApp\Core\Sidebar($this);

    array_walk($this->getRegisteredModules(), function($moduleClass) {
      $module = new $moduleClass($this);
      $module->addRouting($this->router);
    });
  }

  public function registerModule(string $module) {
    if (!in_array($module, $this->registeredModules)) {
      $this->registeredModules[] = $module;
    }
  }

  public function getRegisteredModules(): array {
    return $this->registeredModules;
  }

  public function getSidebar(): \CeremonyCrmApp\Core\Sidebar {
    return $this->sidebar;
  }
}
