<?php

// autoloader pre HubletoMain
spl_autoload_register(function (string $class) {
  $class = str_replace('\\', '/', $class);
  
  $hubletoMain = $GLOBALS['hubletoMain'] ?? null;
  if ($hubletoMain) {
    $rootFolder = $hubletoMain->config->getAsString('rootFolder');
    $premiumAppsFolder = $hubletoMain->config->getAsString('premiumRepoFolder');
    $externalAppsFolder = $hubletoMain->config->getAsString('externalRepoFolder');
  } else {
    $rootFolder = '';
    $premiumAppsFolder = '';
    $externalAppsFolder = '';
  }

  $classGroups = [
    // main
    'HubletoMain/Core/' => __DIR__ . '/../core/',
    'HubletoMain/Cli/' => __DIR__ . '/../cli/',
    'HubletoMain/Hook/' => __DIR__ . '/../hooks/',
    'HubletoMain/Cron/' => __DIR__ . '/../crons/',
    'HubletoMain/Report/' => __DIR__ . '/../reports/',
    'HubletoMain/Installer/' => __DIR__ . '/../installer/',

    // apps
    'HubletoApp/Community/' => __DIR__ . '/../apps/',
    'HubletoApp/Custom/' => $rootFolder . '/apps/',
    'HubletoApp/Premium/' => $premiumAppsFolder . '/',
    'HubletoApp/External/' => $externalAppsFolder . '/',

    // project
    'HubletoProject/Hook/' => $rootFolder . '/hooks/',
    'HubletoProject/Cron/' => $rootFolder . '/crons/',
    'HubletoProject/Report/' => $rootFolder . '/reports/',
    'HubletoProject/Dependency/' => $rootFolder . '/dependencies/',
  ];

  foreach ($classGroups as $group => $folder) {
    if (str_starts_with($class, $group)) {
      include($folder . str_replace($group, '', $class) . '.php');
      break;
    }
  }

});

/**
 * Main Hubleto class. This class is always referenced
 * as `$this->main` or `$main`.
 */
class HubletoMain extends \ADIOS\Core\Loader
{

  protected \Twig\Loader\FilesystemLoader $twigLoader;

  public \HubletoMain\Core\ReleaseManager $release;
  public \HubletoMain\Core\AppManager $apps;
  public \HubletoMain\Core\Emails\EmailProvider $email;
  public \HubletoMain\Core\Emails\EmailWrapper $emails;
  public \HubletoMain\Cli\Agent\Loader $cli;
  public \HubletoMain\Core\HookManager $hooks;
  public \HubletoMain\Core\CronManager $crons;

  /**
   * If set to true, this run is managed as premium.
   *
   * @var bool
   */
  public bool $isPremium = false;

  /**
   * Class construtor.
   *
   * @param array $config
   * @param int $mode
   * 
   */
  public function __construct(array $config = [], int $mode = self::ADIOS_MODE_FULL)
  {
    $this->setAsGlobal();

    parent::__construct($config, $mode);

    // CLI
    $this->cli = $this->di->create(\HubletoMain\Cli\Agent\Loader::class);

    // Hooks
    $this->hooks = $this->di->create(\HubletoMain\Core\HookManager::class);

    // Crons
    $this->crons = $this->di->create(\HubletoMain\Core\CronManager::class);

    // Release manager
    $this->release = $this->di->create(\HubletoMain\Core\ReleaseManager::class);

    // Emails
    $this->email = $this->di->create(\HubletoMain\Core\Emails\EmailProvider::class);

    // DEPRECATED
    $this->emails = $this->di->create(\HubletoMain\Core\Emails\EmailWrapper::class);
    $this->emailProvider = $this->email;

    // App manager
    $this->apps = $this->di->create(\HubletoMain\Core\AppManager::class);

    // Finish
    $this->hooks->run('core:bootstrap-end', [$this]);

  }

  /**
   * Init phase after constructing.
   *
   * @return void
   * 
   */
  public function init(): void
  {

    try {

      if ($this->mode == self::ADIOS_MODE_FULL) {
        $this->session->start(true);
        $this->initDatabaseConnections();
        $this->config->loadFromDB();
      }

      $this->auth->init();
      $this->hooks->init();
      $this->release->init();
      $this->email->init();
      $this->apps->init();
      $this->permissions->init();

      $this->hooks->run('core:init-end', [$this]);
    } catch (\Exception $e) {
      echo "HUBLETO INIT failed: [".get_class($e)."] ".$e->getMessage() . "\n";
      echo $e->getTraceAsString() . "\n";
      exit;
    }
  }

  /**
   * Set $this as the global instance of Hubleto.
   *
   * @return void
   * 
   */
  public function setAsGlobal()
  {
    $GLOBALS['hubletoMain'] = $this;
  }

  /**
   * Creates object for HTML rendering (Twig).
   *
   * @return void
   * 
   */
  public function createTwig(): void
  {

    $this->twigLoader = new \Twig\Loader\FilesystemLoader();
    $this->twigLoader->addPath(__DIR__ . '/../views', 'hubleto');
    $this->twigLoader->addPath(__DIR__ . '/../apps', 'app');
    $this->twigLoader->addPath($this->config->getAsString('rootFolder') . '/views', 'project');

    $this->twig = new \Twig\Environment($this->twigLoader, array(
      'cache' => false,
      'debug' => true,
    ));

    $this->twig->addFunction(new \Twig\TwigFunction(
      'number',
      function (string $amount) {
        return number_format((float) $amount, 2, ",", " ");
      }
    ));

    $this->configureTwig();
  }

  /**
   * Adds namespace for Twig renderer
   *
   * @param string $folder
   * @param string $namespace
   * 
   * @return void
   * 
   */
  public function addTwigViewNamespace(string $folder, string $namespace)
  {
    if (isset($this->twigLoader) && is_dir($folder)) {
      $this->twigLoader->addPath($folder, $namespace);
    }
  }

  /**
   * Create dependency injection service
   *
   * @return \ADIOS\Core\DependencyInjection
   * 
   */
  public function createDependencyInjection(): \ADIOS\Core\DependencyInjection
  {
    return new \HubletoMain\Core\DependencyInjection($this);
  }

  /**
   * Create authentication provider
   *
   * @return \ADIOS\Core\Auth
   * 
   */
  public function createAuthProvider(): \ADIOS\Core\Auth
  {
    return $this->di->create(\HubletoMain\Core\AuthProvider::class);
  }

  /**
   * Create router
   *
   * @return \ADIOS\Core\Router
   * 
   */
  public function createRouter(): \ADIOS\Core\Router
  {
    return $this->di->create(\HubletoMain\Core\Router::class);
  }

  /**
   * Create permission manager
   *
   * @return \ADIOS\Core\Permissions
   * 
   */
  public function createPermissionsManager(): \ADIOS\Core\Permissions
  {
    return $this->di->create(\HubletoMain\Core\Permissions::class);
  }

  /**
   * Create translator
   *
   * @return \HubletoMain\Core\Translator
   * 
   */
  public function createTranslator(): \HubletoMain\Core\Translator
  {
    return $this->di->create(\HubletoMain\Core\Translator::class);
  }

  // /**
  //  * Create default controller for rendering desktop
  //  *
  //  * @return \HubletoMain\Core\Controllers\Controller
  //  * 
  //  */
  // public function createDesktopController(): \HubletoMain\Core\Controllers\Controller
  // {
  //   return $this->di->create(\HubletoMain\Core\Controllers\Controller::class);
  // }

  /**
   * Load dictionary for the specified language
   *
   * @param string $language
   * 
   * @return array
   * 
   */
  public static function loadDictionary(string $language): array
  {
    $dict = [];
    if (strlen($language) == 2) {
      $dictFilename = __DIR__ . '/../lang/' . $language . '.json';
      if (is_file($dictFilename)) {
        $dict = (array) @json_decode((string) file_get_contents($dictFilename), true);
      }
    }
    return $dict;
  }

  /**
   * Callback called before the rendering starts.
   *
   * @return void
   * 
   */
  public function onBeforeRender(): void
  {
    $this->apps->onBeforeRender();
  }

  /**
   * Adds a string to the dictionary
   *
   * @param string $language
   * @param string $contextInner
   * @param string $string
   * 
  * @return array|array<string, array<string, string>>
   * 
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

  /**
   * Renders nicely HTML-formatted strings for specific exceptions
   *
   * @param mixed $exception
   * @param array $args
   * 
   * @return string
   * 
   */
  public function renderExceptionHtml($exception, array $args = []): string
  {
    switch (get_class($exception)) {
      case 'Illuminate\Database\QueryException':
        $dbQuery = $exception->getSql();
        $dbError = $exception->errorInfo[2];
        $errorNo = $exception->errorInfo[1];

        if (in_array($errorNo, [1216, 1451])) {
          $model = $args[0];
          $errorMessage =
            "{$model->shortName} cannot be deleted because other data is linked to it."
          ;
        } elseif (in_array($errorNo, [1062, 1217, 1452])) {
          $errorMessage = "You are trying to save a record that is already existing.";
        } else {
          $errorMessage = $dbError;
        }
        $html = $this->translate($errorMessage);
        break;
      default:
        $html = parent::renderExceptionHtml($exception);
        break;
    }

    return $html;
  }

}
