<?php

use \ADIOS\Core\Helper;

// load configs
require_once(__DIR__ . "/../ConfigApp.php");

// autoloader pre HubletoMain
spl_autoload_register(function($class) {
  $class = str_replace('\\', '/', $class);

  // cli
  if (str_starts_with($class, 'HubletoMain/Cli/')) {
    @include(__DIR__ . '/cli/' . str_replace('HubletoMain/Cli/', '', $class) . '.php');
  }

  // community
  if (str_starts_with($class, 'HubletoApp/Community/')) {
    $dir = defined('HUBLETO_COMMUNITY_REPO') ? HUBLETO_COMMUNITY_REPO : realpath(__DIR__ . '/../apps/community');
    @include($dir . '/' . str_replace('HubletoApp/Community/', '', $class) . '.php');
  }

  // core
  if (str_starts_with($class, 'HubletoMain/Core/')) {
    @include(__DIR__ . '/core/' . str_replace('HubletoMain/Core/', '', $class) . '.php');
  }

  // enterprise
  if (str_starts_with($class, 'HubletoApp/Enterprise/')) {
    $dir = defined('HUBLETO_ENTERPRISE_REPO') ? HUBLETO_ENTERPRISE_REPO : realpath(__DIR__ . '/../apps/enterprise');
    @include(HUBLETO_ENTERPRISE_REPO . '/' . str_replace('HubletoApp/Enterprise/', '', $class) . '.php');
  }

  // external
  if (str_starts_with($class, 'HubletoApp/External/')) {
    $dir = defined('HUBLETO_EXTERNAL_REPO') ? HUBLETO_EXTERNAL_REPO : realpath(__DIR__ . '/../apps/external');
    @include(HUBLETO_EXTERNAL_REPO . '/' . str_replace('HubletoApp/External/', '', $class) . '.php');
  }

  // installer
  if (str_starts_with($class, 'HubletoMain/Installer/')) {
    @include(__DIR__ . '/installer/' . str_replace('HubletoMain/Installer/', '', $class) . '.php');
  }
});

// create own ADIOS class
class HubletoMain extends \ADIOS\Core\Loader
{

  const RELEASE = 'v0.4';

  protected \Twig\Loader\FilesystemLoader $twigLoader;

  public array $apps = [];
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

    $this->registerApp(\HubletoApp\Community\Dashboard\Loader::class);
    $this->registerApp(\HubletoApp\Community\Customers\Loader::class);
    $this->registerApp(\HubletoApp\Community\Calendar\Loader::class);
    $this->registerApp(\HubletoApp\Community\Settings\Loader::class);
    $this->registerApp(\HubletoApp\Community\Help\Loader::class);

    foreach ($this->config['apps'] ?? [] as $appClass => $appConfig) {
      if (($appConfig['enabled'] ?? false) && $appClass::canBeAdded($this)) {
        $this->registerApp($appClass);
      }
    }

    $this->help = new \HubletoMain\Core\Help($this);
    $this->sidebar = new \HubletoMain\Core\Sidebar($this);

    $apps = $this->getRegisteredApps();
    array_walk($apps, function($app) {
      $app->init();
    });

  }

  public function initTwig()
  {
    $this->twigLoader = new \Twig\Loader\FilesystemLoader();
    $this->twigLoader->addPath(__DIR__ . '/views', 'hubleto');
    $this->twigLoader->addPath(__DIR__ . '/../apps', 'app');

    $this->twig = new \Twig\Environment($this->twigLoader, array(
      'cache' => FALSE,
      'debug' => TRUE,
    ));
  }

  public function registerApp(string $app)
  {
    if (!in_array($app, $this->apps)) {
      $this->apps[$app] = new $app($this);
    }
  }

  public function getRegisteredApps(): array
  {
    return $this->apps;
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
