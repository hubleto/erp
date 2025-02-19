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
    // $dir = (string) (defined('HUBLETO_EXTERNAL_REPO') ? HUBLETO_EXTERNAL_REPO : realpath(__DIR__ . '/../apps/external'));
    $tmp = str_replace('HubletoApp/External/', '', $class);
    $vendor = substr($tmp, 0, strpos($tmp, '/'));
    $app = substr($tmp, strpos($tmp, '/') + 1);
    $hubletoMain = $GLOBALS['hubletoMain'];
    $externalAppsRepositories = $hubletoMain->configAsArray('externalAppsRepositories');
    $folder = $externalAppsRepositories[$vendor] ?? '';

    @include($folder . '/' . $app . '.php');
  }

  // installer
  if (str_starts_with($class, 'HubletoMain/Installer/')) {
    @include(__DIR__ . '/installer/' . str_replace('HubletoMain/Installer/', '', $class) . '.php');
  }
});

// create own ADIOS class
class HubletoMain extends \ADIOS\Core\Loader
{

  const RELEASE = 'v0.8';

  protected \Twig\Loader\FilesystemLoader $twigLoader;


  public \HubletoMain\Core\Sidebar $sidebar;
  public \HubletoMain\Core\Help $help;
  public \HubletoMain\Core\CalendarManager $calendarManager;
  public \HubletoMain\Core\ReportManager $reportManager;
  public \HubletoMain\Core\AppManager $appManager;

  public string $requestedUriFirstPart = '';
  public bool $isPro = false;

  private array $settings = [];

  public string $twigNamespaceCore = 'hubleto';

  public function __construct(array $config = [], int $mode = self::ADIOS_MODE_FULL)
  {
    $this->setAsGlobal();

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

    $this->calendarManager = new \HubletoMain\Core\CalendarManager($this);
    $this->reportManager = new \HubletoMain\Core\ReportManager($this);

    $this->appManager = new \HubletoMain\Core\AppManager($this);

    foreach ($this->appManager->getInstalledAppClasses() as $appClass => $appConfig) {
      $appClass = (string) $appClass;
      if (is_array($appConfig) && $appClass::canBeAdded($this)) {
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

  public function setAsGlobal() {
    $GLOBALS['hubletoMain'] = $this;
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

  public function addTwigViewNamespace(string $folder, string $namespace) {
    if (isset($this->twigLoader) && is_dir($folder)) {
      $this->twigLoader->addPath($folder, $namespace);
    }
  }

  public function getSidebar(): \HubletoMain\Core\Sidebar
  {
    return $this->sidebar;
  }

  public function createTranslator(): \HubletoMain\Core\Translator
  {
    return new \HubletoMain\Core\Translator($this);
  }

  public function createDesktopController(): \HubletoMain\Core\Controller
  {
    return new \HubletoMain\Core\Controller($this);
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

  public static function loadDictionary(string $language): array
  {
    $dict = [];
    if (strlen($language) == 2) {
      $dictFilename = __DIR__ . '/../lang/' . $language . '.json';
      if (is_file($dictFilename)) $dict = (array) @json_decode((string) file_get_contents($dictFilename), true);
    }
    return $dict;
  }

}
