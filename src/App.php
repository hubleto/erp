<?php

use \ADIOS\Core\Helper;

// load configs
require_once(__DIR__ . "/../ConfigApp.php");

// autoloader pre HubletoMain
spl_autoload_register(function($class) {
  $class = str_replace('\\', '/', $class);
  if (str_starts_with($class, 'HubletoApp/')) {
    require_once(__DIR__ . '/../apps/' . str_replace('HubletoApp/', '', $class) . '.php');
  } else if (str_starts_with($class, 'HubletoMain/Core/')) {
    require_once(__DIR__ . '/core/' . str_replace('HubletoMain/Core/', '', $class) . '.php');
  } else if (str_starts_with($class, 'HubletoMain/Installer/')) {
    require_once(__DIR__ . '/installer/' . str_replace('HubletoMain/Installer/', '', $class) . '.php');
  }
});

// create own ADIOS class
class HubletoMain extends \ADIOS\Core\Loader
{
  protected \Twig\Loader\FilesystemLoader $twigLoader;

  protected array $modules = [];
  public \HubletoMain\Core\Sidebar $sidebar;

  public string $requestedUriFirstPart = '';
  public bool $isPro = false;
  private array $calendars = [];
  private array $settings = [];

  public string $twigNamespaceCore = 'hubleto';

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

    $this->addModule(\HubletoApp\Dashboard\Loader::class);
    $this->addModule(\HubletoApp\Customers\Loader::class);
    $this->addModule(\HubletoApp\Calendar\Loader::class);
    $this->addModule(\HubletoApp\Settings\Loader::class);
    $this->addModule(\HubletoApp\Help\Loader::class);

    foreach ($this->config['enabledModules'] ?? [] as $module) {
      if ($module::canBeAdded($this)) {
        $this->addModule($module);
      }
    }

    $this->help = new \HubletoMain\Core\Help($this);
    $this->sidebar = new \HubletoMain\Core\Sidebar($this);

    $modules = $this->getModules();
    array_walk($modules, function($module) {
      $module->init();
    });

  }

  public function initTwig()
  {
    $this->twigLoader = new \Twig\Loader\FilesystemLoader();
    $this->twigLoader->addPath(__DIR__ . '/core', 'hubleto');
    $this->twigLoader->addPath(__DIR__ . '/../apps', 'app');

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

  public function getSidebar(): \HubletoMain\Core\Sidebar
  {
    return $this->sidebar;
  }

  public function getTranslator(): \HubletoMain\Core\Translator
  {
    return new \HubletoMain\Core\Translator($this);
  }

  public function getDesktopController(): \HubletoMain\Core\Controller
  {
    return new \HubletoMain\Core\Controller($this);
  }

  public function addCalendar(string $calendarClass)
  {
    $this->calendars[$calendarClass] = new $calendarClass($this);
  }

  public function getCalendars(): array
  {
    return $this->calendars;
  }

  public function getCalendar(string $calendarClass): \HubletoMain\Core\Calendar
  {
    return $this->calendars[$calendarClass];
  }

  public function addSetting(array $setting)
  {
    $this->settings[] = $setting;
  }

  public function getSettings(): array
  {
    $settings = $this->settings;
    $titles = array_column($this->settings, 'title');
    array_multisort($titles, SORT_ASC, $settings);

    return $settings;

  }

}
