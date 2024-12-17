<?php

use \ADIOS\Core\Helper;

// load configs
require_once(__DIR__ . "/../ConfigApp.php");

// autoloader pre CeremonyCrmApp
spl_autoload_register(function($class) {
  $class = str_replace('\\', '/', $class);
  if (str_starts_with($class, 'CeremonyCrmMod/')) {
    require_once(__DIR__ . '/modules/' . str_replace('CeremonyCrmMod/', '', $class) . '.php');
  } else if (str_starts_with($class, 'CeremonyCrmApp/Core/')) {
    require_once(__DIR__ . '/core/' . str_replace('CeremonyCrmApp/Core/', '', $class) . '.php');
  } else if (str_starts_with($class, 'CeremonyCrmApp/Installer/')) {
    require_once(__DIR__ . '/installer/' . str_replace('CeremonyCrmApp/Installer/', '', $class) . '.php');
  } else if (str_starts_with($class, 'CeremonyCrmApp/') && !str_starts_with($class, 'CeremonyCrmApp/Extensions/')) {
    require_once(__DIR__ . '/core/' . str_replace('CeremonyCrmApp/', '', $class) . '.php');
  }
});

// create own ADIOS class
class CeremonyCrmApp extends \ADIOS\Core\Loader
{
  protected \Twig\Loader\FilesystemLoader $twigLoader;

  protected array $modules = [];
  public \CeremonyCrmApp\Core\Sidebar $sidebar;

  public string $requestedUriFirstPart = '';
  protected array $extensions = [];
  public bool $isPro = false;
  private array $calendars;

  public function __construct($config = NULL, $mode = NULL)
  {
    parent::__construct($config, $mode);

    $tmp =  strpos($this->requestedUri, '/');
    if ($tmp === false) $this->requestedUriFirstPart = $this->requestedUri;
    else $this->requestedUriFirstPart = substr($this->requestedUri, 0, strpos($this->requestedUri, '/'));

    $this->config['language'] = $this->auth->user['language'] ?? 'en';

    if (is_file($this->config['accountDir'] . '/pro')) {
      $this->isPro = file_get_contents($this->config['accountDir'] . '/pro') == '1';
    }

    if ($mode == self::ADIOS_MODE_FULL) {
      $this->twig->addFunction(new \Twig\TwigFunction(
        'number',
        function ($amount) { return number_format($amount, 2, ",", " "); }
      ));
    }

    foreach ($this->config['enabledModules'] ?? [] as $module) {
      if ($module::canBeAdded($this)) {
        $this->addModule($module);
      }
    }

    foreach ($this->getInstalledExtensionNames() as $extName) {
      $this->addExtension($extName);
    }

    $this->help = new \CeremonyCrmApp\Core\Help($this);
    $this->sidebar = new \CeremonyCrmApp\Core\Sidebar($this);

    $modules = $this->getModules();
    array_walk($modules, function($module) {
      $module->init();
    });

    foreach ($this->extensions as $extName => $extension) {
      $extNameSanitized = str_replace('/', '-', str_replace('\\', '-', $extName));
      $extension->init();

      $this->twigLoader->addPath($extension->rootFolder . '/src/Views', 'ext-' . $extNameSanitized);
    }

    // var_dump($this->sidebar->items);exit;
    // var_dump($this->extractRouteFromRequest());
    // var_dump($this->router->routing);
    // var_dump($this->router->applyRouting($this->extractRouteFromRequest(), []));exit;
  }

  public function initTwig()
  {
    $this->twigLoader = new \Twig\Loader\FilesystemLoader();
    $this->twigLoader->addPath(__DIR__ . '/core', 'app');
    $this->twigLoader->addPath(__DIR__ . '/core', 'core');
    $this->twigLoader->addPath(__DIR__ . '/modules', 'mod');

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

  public function getTranslator(): \CeremonyCrmApp\Core\Translator
  {
    return new \CeremonyCrmApp\Core\Translator($this);
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

  public function addCalendar(string $calendarClass)
  {
    $this->calendars[$calendarClass] = new $calendarClass($this);
  }

  public function getCalendars(): array
  {
    return $this->calendars;
  }

  public function getCalendar(string $calendarClass): \CeremonyCrmApp\Core\Calendar
  {
    return $this->calendars[$calendarClass];
  }

}
