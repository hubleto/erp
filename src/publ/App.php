<?php

use \ADIOS\Core\Helper;

// load configs
require_once(__DIR__ . "/ConfigApp.php");

// autoloader pre CeremonyCrmApp
spl_autoload_register(function($class) {
  $class = str_replace('\\', '/', $class);
  if (str_starts_with($class, 'CeremonyCrmApp/') && !str_starts_with($class, 'CeremonyCrmApp/Extensions/')) {
    require_once(__DIR__ . '/' . str_replace('CeremonyCrmApp/', '', $class) . '.php');
  }
});

// create own ADIOS class
class CeremonyCrmApp extends \ADIOS\Core\Loader
{
  protected \Twig\Loader\FilesystemLoader $twigLoader;

  protected array $modules = [];
  protected \CeremonyCrmApp\Core\Sidebar $sidebar;

  protected array $extensions = [];

  public function __construct($config = NULL, $mode = NULL)
  {
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

    $this->addModule(\CeremonyCrmApp\Modules\Core\Dashboard\Loader::class);
    $this->addModule(\CeremonyCrmApp\Modules\Core\Settings\Loader::class);
    $this->addModule(\CeremonyCrmApp\Modules\Core\Customers\Loader::class);
    $this->addModule(\CeremonyCrmApp\Modules\Core\Documents\Loader::class);
    $this->addModule(\CeremonyCrmApp\Modules\Core\Calendar\Loader::class);
    $this->addModule(\CeremonyCrmApp\Modules\Sales\Deals\Loader::class);
    $this->addModule(\CeremonyCrmApp\Modules\Sales\Leads\Loader::class);
    $this->addModule(\CeremonyCrmApp\Modules\Sales\Core\Loader::class);
    $this->addModule(\CeremonyCrmApp\Modules\Core\Billing\Loader::class);
    $this->addModule(\CeremonyCrmApp\Modules\Core\Services\Loader::class);
    $this->addModule(\CeremonyCrmApp\Modules\Core\Support\Loader::class);
    $this->addModule(\CeremonyCrmApp\Modules\Core\Extensions\Loader::class);

    foreach ($this->getInstalledExtensionNames() as $extName) {
      $this->addExtension($extName);
    }

    $this->sidebar = new \CeremonyCrmApp\Core\Sidebar($this);

    array_walk($this->getModules(), function($module) {
      $module->init();
    });

    foreach ($this->extensions as $extName => $extension) {
      $extNameSanitized = str_replace('/', '-', str_replace('\\', '-', $extName));
      $extension->init();

      $this->twigLoader->addPath($extension->rootFolder . '/src/Views', 'ext-' . $extNameSanitized);
    }

    // var_dump($this->extractRouteFromRequest());
    // var_dump($this->router->routing);
    // var_dump($this->router->applyRouting($this->extractRouteFromRequest(), []));exit;

  }

  public function initTwig()
  {
    $this->twigLoader = new \Twig\Loader\FilesystemLoader();
    $this->twigLoader->addPath(__DIR__, 'app');

    $this->twig = new \Twig\Environment($this->twigLoader, array(
      'cache' => FALSE,
      'debug' => TRUE,
    ));
  }

  public function addModule(string $module)
  {
    if (!in_array($module, $this->modules)) {
      $this->modules[$module] = new $module($this);
    }
  }

  public function getModules(): array
  {
    return $this->modules;
  }

  public function getSidebar(): \CeremonyCrmApp\Core\Sidebar
  {
    return $this->sidebar;
  }

  public function getDesktopController(): \CeremonyCrmApp\Core\Controller
  {
    return new \CeremonyCrmApp\Core\Controller($this);
  }

  public function addExtension(string $extName)
  {
    $extClass = '\\CeremonyCrmApp\\Extensions\\' . $extName . '\\Loader';
    $this->extensions[$extName] = new $extClass($this);
  }

  public function getInstalledExtensionNames(): array
  {
    if (isset($this->config['extensions']) && is_array($this->config['extensions'])) {
      return $this->config['extensions'];
    } else {
      return [];
    }
  }

  public function getExtensions(): array
  {
    return $this->extensions;
  }

}
