<?php

use \ADIOS\Core\Helper;

// load configs
require_once(__DIR__ . "/../ConfigApp.php");

// autoloader pre HubletoMain
spl_autoload_register(function(string $class) {
  $class = str_replace('\\', '/', $class);

  // cli
  if (str_starts_with($class, 'HubletoMain/Cli/')) {
    @include(__DIR__ . '/cli/' . str_replace('HubletoMain/Cli/', '', $class) . '.php');
  }

  // community
  if (str_starts_with($class, 'HubletoApp/Community/')) {
    $dir = (string) (defined('HUBLETO_COMMUNITY_REPO') ? HUBLETO_COMMUNITY_REPO : realpath(__DIR__ . '/../apps/community'));
    @include($dir . '/' . str_replace('HubletoApp/Community/', '', $class) . '.php');
  }

  // core
  if (str_starts_with($class, 'HubletoMain/Core/')) {
    @include(__DIR__ . '/core/' . str_replace('HubletoMain/Core/', '', $class) . '.php');
  }

  // enterprise
  if (str_starts_with($class, 'HubletoApp/Enterprise/')) {
    $dir = (string) (defined('HUBLETO_ENTERPRISE_REPO') ? HUBLETO_ENTERPRISE_REPO : realpath(__DIR__ . '/../apps/enterprise'));
    @include($dir . '/' . str_replace('HubletoApp/Enterprise/', '', $class) . '.php');
  }

  // external
  if (str_starts_with($class, 'HubletoApp/External/')) {
    $dir = (string) (defined('HUBLETO_EXTERNAL_REPO') ? HUBLETO_EXTERNAL_REPO : realpath(__DIR__ . '/../apps/external'));
    @include($dir . '/' . str_replace('HubletoApp/External/', '', $class) . '.php');
  }

  // installer
  if (str_starts_with($class, 'HubletoMain/Installer/')) {
    @include(__DIR__ . '/installer/' . str_replace('HubletoMain/Installer/', '', $class) . '.php');
  }
});

// create own ADIOS class
class HubletoMain extends \ADIOS\Core\Loader
{

  const RELEASE = 'v0.5';

  protected \Twig\Loader\FilesystemLoader $twigLoader;


  public \HubletoMain\Core\Sidebar $sidebar;
  public \HubletoMain\Core\Help $help;
  public \HubletoMain\Core\AppManager $appManager;

  public string $requestedUriFirstPart = '';
  public bool $isPro = false;

  /** @var array<string, \HubletoMain\Core\Calendar> */
  private array $calendars = [];

  private array $settings = [];

  public string $twigNamespaceCore = 'hubleto';

  public function __construct(array $config = [], int $mode = self::ADIOS_MODE_FULL)
  {
    parent::__construct($config, $mode);

    $tmp =  strpos($this->requestedUri, '/');
    if ($tmp === false) $this->requestedUriFirstPart = $this->requestedUri;
    else $this->requestedUriFirstPart = substr($this->requestedUri, 0, (int) strpos($this->requestedUri, '/'));

    $userLanguage = $this->auth->getUserLanguage();
    if (empty($userLanguage)) $userLanguage = 'en';
    $this->config['language'] = $userLanguage;

    if (is_file($this->configAsString('accountDir', '') . '/pro')) {
      $this->isPro = (string) file_get_contents($this->configAsString('accountDir', '') . '/pro') == '1';
    }

    if ($mode == self::ADIOS_MODE_FULL) {
      $this->twig->addFunction(new \Twig\TwigFunction(
        'number',
        function (string $amount) {
          return number_format((float) $amount, 2, ",", " ");
        }
      ));
    }

    $this->appManager = new \HubletoMain\Core\AppManager($this);

    $this->appManager->registerApp(\HubletoApp\Community\Dashboard\Loader::class);
    $this->appManager->registerApp(\HubletoApp\Community\Customers\Loader::class);
    $this->appManager->registerApp(\HubletoApp\Community\Calendar\Loader::class);
    $this->appManager->registerApp(\HubletoApp\Community\Settings\Loader::class);
    $this->appManager->registerApp(\HubletoApp\Community\Help\Loader::class);

    foreach ($this->appManager->getInstalledApps() as $appClass => $appConfig) {
      $appClass = (string) $appClass;
      if (is_array($appConfig) && ($appConfig['enabled'] ?? false) && $appClass::canBeAdded($this)) {
        $this->appManager->registerApp($appClass);
      }
    }

    $this->help = new \HubletoMain\Core\Help($this);
    $this->sidebar = new \HubletoMain\Core\Sidebar($this);

    $apps = $this->appManager->getRegisteredApps();
    array_walk($apps, function($app) {
      $app->init();
    });

  }

  public function initTwig(): void
  {
    $this->twigLoader = new \Twig\Loader\FilesystemLoader();
    $this->twigLoader->addPath(__DIR__ . '/views', 'hubleto');
    $this->twigLoader->addPath(__DIR__ . '/../apps', 'app');

    $this->twig = new \Twig\Environment($this->twigLoader, array(
      'cache' => FALSE,
      'debug' => TRUE,
    ));
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

  public function addCalendar(string $calendarClass): void
  {
    $calendar = new $calendarClass($this);
    if ($calendar instanceof \HubletoMain\Core\Calendar) {
      $this->calendars[$calendarClass] = $calendar;
    }
  }

  public function getCalendars(): array
  {
    return $this->calendars;
  }

  public function getCalendar(string $calendarClass): \HubletoMain\Core\Calendar
  {
    return $this->calendars[$calendarClass];
  }

  public function addSetting(array $setting): void
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
