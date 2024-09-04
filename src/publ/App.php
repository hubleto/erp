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

    $setLanguage = $this->params['set-language'] ?? '';

    if (
      !empty($setLanguage)
      && !empty(\CeremonyCrmApp\Modules\Core\Settings\Models\User::ENUM_LANGUAGES[$setLanguage])
    ) {
      $mUser = new \CeremonyCrmApp\Modules\Core\Settings\Models\User($this);
      $mUser->eloquent
        ->where('id', $this->userProfile['id'])
        ->update(['language' => $setLanguage])
      ;
      $this->userProfile['language'] = $setLanguage;
    }

    $this->config['language'] = $this->userProfile['language'] ?? 'en';

    if ($mode == self::ADIOS_MODE_FULL) {
      $this->twig->addFunction(new \Twig\TwigFunction(
        'number',
        function ($amount) { return number_format($amount, 2, ",", " "); }
      ));
    }

    $this->registerModule(\CeremonyCrmApp\Modules\Core\Dashboard\Loader::class);
    $this->registerModule(\CeremonyCrmApp\Modules\Core\Settings\Loader::class);
    $this->registerModule(\CeremonyCrmApp\Modules\Core\Customers\Loader::class);
    $this->registerModule(\CeremonyCrmApp\Modules\Core\Support\Loader::class);
    $this->registerModule(\CeremonyCrmApp\Modules\Core\Sandbox\Loader::class);
    $this->registerModule(\CeremonyCrmApp\Modules\Core\Billing\Loader::class);
    $this->registerModule(\CeremonyCrmApp\Modules\Core\Services\Loader::class);

    $this->sidebar = new \CeremonyCrmApp\Core\Sidebar($this);

    $registeredModules = $this->getRegisteredModules();
    array_walk($registeredModules, function($moduleClass) {
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

  public function getDesktopController(): \CeremonyCrmApp\Core\Controller {
    return new \CeremonyCrmApp\Core\Controller($this);
  }
}
