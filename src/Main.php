<?php

// autoloader pre HubletoMain
spl_autoload_register(function(string $class) {
  $class = str_replace('\\', '/', $class);

  // cli
  if (str_starts_with($class, 'HubletoMain/Cli/')) {
    @include(__DIR__ . '/cli/' . str_replace('HubletoMain/Cli/', '', $class) . '.php');
  }

  // hook
  if (str_starts_with($class, 'HubletoMain/Hook/')) {
    @include(__DIR__ . '/../hooks/' . str_replace('HubletoMain/Hook/', '', $class) . '.php');
  }

  // community
  if (str_starts_with($class, 'HubletoApp/Community/')) {
    $dir = (string) realpath(__DIR__ . '/../apps/community');
    @include($dir . '/' . str_replace('HubletoApp/Community/', '', $class) . '.php');
  }

  // core
  if (str_starts_with($class, 'HubletoMain/Core/')) {
    @include(__DIR__ . '/core/' . str_replace('HubletoMain/Core/', '', $class) . '.php');
  }

  // premium
  if (str_starts_with($class, 'HubletoApp/Premium/')) {
    $hubletoMain = $GLOBALS['hubletoMain'];
    $dir = (string) $hubletoMain->config->getAsString('premiumRepoFolder');
    if (!empty($dir)) {
      @include($dir . '/' . str_replace('HubletoApp/Premium/', '', $class) . '.php');
    }
  }

  // external
  if (str_starts_with($class, 'HubletoApp/External/')) {
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
  public \HubletoMain\Core\AppManager $apps;
  public \HubletoMain\Core\Emails\EmailWrapper $emails;
  public \HubletoMain\Cli\Agent\Loader $cli;

  public array $hooks = [];

  public bool $isPremium = false;

  public function __construct(array $config = [], int $mode = self::ADIOS_MODE_FULL)
  {
    $this->setAsGlobal();

    parent::__construct($config, $mode);

    $this->cli = new \HubletoMain\Cli\Agent\Loader($this);

    $hooks = \ADIOS\Core\Helper::scanDirRecursively(__DIR__ . '/../hooks');
    foreach ($hooks as $hook) {
      $hookClass = '\\HubletoMain\\Hook\\' . str_replace('/', '\\', $hook);
      $hookClass = str_replace('.php', '', $hookClass);
      $this->hooks[$hook] = new $hookClass($this, $this->cli);
    }

    $this->release = new \HubletoMain\Core\ReleaseManager($this);
    $this->release->load();

    $this->emails = new \HubletoMain\Core\Emails\EmailWrapper(
      $this,
      new \HubletoMain\Core\Emails\EmailProvider(
        $this->config->getAsString('smtpHost', ''),
        $this->config->getAsString('smtpPort', ''),
        $this->config->getAsString('smtpEncryption', 'ssl'),
        $this->config->getAsString('smtpLogin', ''),
        $this->config->getAsString('smtpPassword', ''),
      )
    );

    $this->apps = new \HubletoMain\Core\AppManager($this);

    $this->permissions->init();
    $this->runHook('core:permissions-initialized', [$this]);

    $this->apps->init();

    $this->runHook('core:apps-initialized', [$this]);

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

  public function createDesktopController(): \HubletoMain\Core\Controllers\Controller
  {
    return new \HubletoMain\Core\Controllers\Controller($this);
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

  public function onBeforeRender(): void
  {
    $this->apps->onBeforeRender();
  }

  /**
  * @return array|array<string, array<string, string>>
  */
  public static function addToDictionary(string $language, string $contextInner, string $string): void
  {

    $dictFilename = static::getDictionaryFilename($language);

    $dict = static::loadDictionary($language);

    $main = \ADIOS\Core\Helper::getGlobalApp();

    if ($main->config->getAsBool('autoTranslate')) {
      $tr = new \Stichoza\GoogleTranslate\GoogleTranslate();
      $tr->setSource('en'); // Translate from
      $tr->setTarget($language); // Translate to
      $dict[$contextInner][$string] = $tr->translate($string);
    } else {
      $dict[$contextInner][$string] = '';
    }


    @file_put_contents($dictFilename, json_encode($dict, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

  }

  public function runHook(string $trigger, array $args) {
    foreach ($this->hooks as $hook) {
      $hook->run($trigger, $args);
    }
  }

}
