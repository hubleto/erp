<?php

namespace HubletoMain;

/**
 * Main Hubleto class. This class is always referenced
 * as `$this->main` or `$main`.
 */
class Loader extends \Hubleto\Framework\Loader
{

  public \HubletoMain\ReleaseManager $release;
  public \HubletoMain\AppManager $apps;
  public \HubletoMain\HookManager $hooks;
  public \HubletoMain\CronManager $crons;

  public \HubletoMain\Emails\EmailProvider $email;
  public \HubletoMain\Emails\EmailWrapper $emails;

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
  public function __construct(array $config = [], int $mode = self::HUBLETO_MODE_FULL)
  {
    parent::__construct($config, $mode);

    // Hooks
    $this->hooks = $this->di->create(\HubletoMain\HookManager::class);

    // Crons
    $this->crons = $this->di->create(\HubletoMain\CronManager::class);

    // Release manager
    $this->release = $this->di->create(\HubletoMain\ReleaseManager::class);

    // Emails
    $this->email = $this->di->create(\HubletoMain\Emails\EmailProvider::class);

    // DEPRECATED
    $this->emails = $this->di->create(\HubletoMain\Emails\EmailWrapper::class);
    $this->emails->emailProvider = $this->email;

    // App manager
    $this->apps = $this->di->create(\HubletoMain\AppManager::class);

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

      if ($this->mode == self::HUBLETO_MODE_FULL) {
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

  public function createRenderer()
  {
    $this->twigLoader = new \Twig\Loader\FilesystemLoader();
    $this->twig = new \Twig\Environment($this->twigLoader, array(
      'cache' => false,
      'debug' => true,
    ));

    $this->configureRenderer();
  }

  /**
   * Creates object for HTML rendering (Twig).
   *
   * @return void
   * 
   */
  public function configureRenderer(): void
  {

    parent::configureRenderer();

    $this->twigLoader->addPath(__DIR__ . '/../views', 'hubleto-main');
    $this->twigLoader->addPath(__DIR__ . '/../../apps/src', 'app');

    if (is_dir($this->config->getAsString('rootFolder') . '/src/views')) {
      $this->twigLoader->addPath($this->config->getAsString('rootFolder') . '/src/views', 'project');
    }

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
   * @return \Hubleto\Framework\DependencyInjection
   * 
   */
  public function createDependencyInjection(): \Hubleto\Framework\DependencyInjection
  {
    return new \Hubleto\Framework\DependencyInjection($this);
  }

  /**
   * Create authentication provider
   *
   * @return \Hubleto\Framework\Auth
   * 
   */
  public function createAuthProvider(): \Hubleto\Framework\Auth
  {
    return $this->di->create(\HubletoMain\AuthProvider::class);
  }

  /**
   * Create router
   *
   * @return \Hubleto\Framework\Router
   * 
   */
  public function createRouter(): \Hubleto\Framework\Router
  {
    return $this->di->create(\HubletoMain\Router::class);
  }

  /**
   * Create permission manager
   *
   * @return \Hubleto\Framework\Permissions
   * 
   */
  public function createPermissionsManager(): \Hubleto\Framework\Permissions
  {
    return $this->di->create(\Hubleto\Framework\Permissions::class);
  }

  /**
   * Create translator
   *
   * @return \Hubleto\Framework\Translator
   * 
   */
  public function createTranslator(): \Hubleto\Framework\Translator
  {
    return $this->di->create(\Hubleto\Framework\Translator::class);
  }

  // /**
  //  * Create default controller for rendering desktop
  //  *
  //  * @return \Hubleto\Framework\Controllers\Controller
  //  * 
  //  */
  // public function createDesktopController(): \Hubleto\Framework\Controllers\Controller
  // {
  //   return $this->di->create(\Hubleto\Framework\Controllers\Controller::class);
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

    $main = \HubletoMain\Loader::getGlobalApp();

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
