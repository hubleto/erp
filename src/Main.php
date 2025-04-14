<?php

use \ADIOS\Core\Helper;

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
    $externalAppsRepositories = $hubletoMain->config->getAsArray('externalAppsRepositories');
    $folder = $externalAppsRepositories[$vendor] ?? '';

    @include($folder . '/' . $app . '.php');
  }

  // community
  if (str_starts_with($class, 'HubletoApp/Custom/')) {
    $hubletoMain = $GLOBALS['hubletoMain'];
    $dir = $hubletoMain->config->getAsString('accountDir') . '/apps/custom';
    @include($dir . '/' . str_replace('HubletoApp/Custom/', '', $class) . '.php');
  }

  // installer
  if (str_starts_with($class, 'HubletoMain/Installer/')) {
    @include(__DIR__ . '/installer/' . str_replace('HubletoMain/Installer/', '', $class) . '.php');
  }
});

// create own ADIOS class
class HubletoMain extends \ADIOS\Core\Loader
{

  const RELEASE = 'v0.10';

  protected \Twig\Loader\FilesystemLoader $twigLoader;

  public \HubletoMain\Core\ReleaseManager $release;
  public \HubletoMain\Core\Sidebar $sidebar;
  public \HubletoMain\Core\Help $help;
  public \HubletoMain\Core\AppManager $apps;

  public bool $isPremium = false;

  private array $settings = [];

  public function __construct(array $config = [], int $mode = self::ADIOS_MODE_FULL)
  {
    $this->setAsGlobal();

    parent::__construct($config, $mode);

    if (is_file($this->config->getAsString('accountDir', '') . '/pro')) {
      $this->isPremium = (string) file_get_contents($this->config->getAsString('accountDir', '') . '/pro') == '1';
    }

    $this->release = new \HubletoMain\Core\ReleaseManager($this);
    $this->release->load();

    $this->apps = new \HubletoMain\Core\AppManager($this);
    $this->sidebar = new \HubletoMain\Core\Sidebar($this);

    $this->permissions->init();
    $this->apps->init();

  }

  public function setAsGlobal() {
    $GLOBALS['hubletoMain'] = $this;
  }

  public function createTwig(): void
  {
  
    $this->twigLoader = new \Twig\Loader\FilesystemLoader();
    $this->twigLoader->addPath(__DIR__ . '/views', 'hubleto');
    $this->twigLoader->addPath(__DIR__ . '/../apps', 'app');

    $this->twig = new \Twig\Environment($this->twigLoader, array(
      'cache' => FALSE,
      'debug' => TRUE,
    ));

    $this->twig->addFunction(new \Twig\TwigFunction(
      'number',
      function (string $amount) {
        return number_format((float) $amount, 2, ",", " ");
      }
    ));

    $this->configureTwig();
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

  public function createAuthProvider(): \ADIOS\Core\Auth
  {
    return new \HubletoMain\Core\AuthProvider($this, []);
  }

  public function createRouter(): \ADIOS\Core\Router
  {
    return new \HubletoMain\Core\Router($this);
  }

  public function createPermissionsManager(): \ADIOS\Core\Permissions
  {
    return new \HubletoMain\Core\Permissions($this);
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
