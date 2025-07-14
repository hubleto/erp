<?php

namespace HubletoMain\Core\Controllers;

class Controller extends \ADIOS\Core\Controller
{

  public \HubletoMain $main;

  public bool $disableLogUsage = false;
  public bool $permittedForAllUsers = false;

  public string $appNamespace = '';
  public \HubletoMain\Core\App $hubletoApp;

  function __construct(\HubletoMain $main)
  {
    $this->main = $main;

    if (empty($this->translationContext)) {
      $reflection = new \ReflectionClass($this);
      preg_match('/^(.*?)\\\Controllers\\\(.*?)$/', $reflection->getName(), $m);
      if (isset($m[1]) && isset($m[2])) {
        $this->appNamespace = $m[1];
        $this->translationContext = $m[1] . '\\Loader::Controllers\\' . $m[2];
      }
    }

    parent::__construct($main);

    if ($this->main->apps->getAppInstance($this->appNamespace)) {
      $this->hubletoApp = $this->main->apps->getAppInstance($this->appNamespace);
    }
  }

  public function activeUserHasPermission(): bool
  {
    if (
      isset($this->hubletoApp)
      && $this->requiresUserAuthentication
      && !$this->permittedForAllUsers
      && !$this->main->permissions->isAppPermittedForActiveUser($this->hubletoApp)
    ) {
      // throw new \ADIOS\Core\Exceptions\NotEnoughPermissionsException("You have no access neither to {$this->hubletoApp->manifest['name']} nor {$this->shortName}.");
      return false;
    }

    return true;
  }

  /**
    * Executed after the init() phase.
    * Validates inputs ($this->main->params) used for the TWIG template.
    *
    * return bool True if inputs are valid, otherwise false.
    */
  public function validateInputs(): bool
  {
    $valid = TRUE;

    return $valid;

  }

  /**
   * Executed at the end of the constructor.
   *
   * @throws \Exception Should throw an exception on error.
   */
  public function init(): void
  {
    $this->main->hooks->run('controller:init-start', [$this]);

    // Put your controller's initialization code here. See example below.
    // Throw an exception on error.

    if (!$this->validateInputs()) {
      throw new \Exception("Malformed URL");
    }
  }

  /**
   * Used to set parametere for the view renderer.
   *
   * @return void
   */
  public function prepareView(): void
  {
    if (!$this->activeUserHasPermission()) return;

    $this->main->hooks->run('controller:prepare-view-start', [$this]);

    $logFolder = $this->app->config->getAsString('logFolder');

    if ($this->main->auth->isUserInSession()) {
      $user = $this->main->auth->getUserFromSession();

      if (!empty($logFolder) && is_dir($logFolder)) {
        if (!is_dir($logFolder . '/usage')) mkdir($logFolder . '/usage');
        file_put_contents(
          $logFolder . '/usage/' . date('Y-m-d') . '.log',
          date('H:i:s') . ' ' . $user['id'] . ' ' . get_class($this) . ' '. json_encode(array_keys($this->main->getUrlParams()), true) . "\n",
          FILE_APPEND
        );
      }
    }

    parent::prepareView();

    $this->viewParams['main'] = $this->main;
    $this->viewParams['currentTheme'] = $this->main->config->getAsString('uiTheme', 'default');

    if (isset($this->hubletoApp)) $this->viewParams['app'] = $this->hubletoApp;
    // $this->viewParams['help'] = $this->main->apps->community('Help');
    $this->viewParams['breadcrumbs'] = $this->getBreadcrumbs();
    $this->viewParams['requestedUri'] = $this->main->requestedUri;

    $contextHelpUrls = $this->main->apps->community('Help')?->getCurrentContextHelpUrls($this->main->route);
    $user = $this->main->auth->getUser();

    if (isset($contextHelpUrls[$user['language']])) $contextHelpUrl = $contextHelpUrls[$user['language']];
    else if (isset($contextHelpUrls['en'])) $contextHelpUrl = $contextHelpUrls['en'];
    else $contextHelpUrl = '';

    $this->viewParams['contextHelpUrl'] = $contextHelpUrl;

    $this->main->hooks->run('controller:prepare-view-end', [$this]);

  }

  public function setView(null|string $view, array|null $viewParams = null)
  {
    if (!$this->activeUserHasPermission()) {
      parent::setView('@hubleto/AccessForbidden.twig', [
        'message' => "You have no access neither to {$this->hubletoApp->manifest['name']} nor {$this->shortName}."
      ]);
    } else {
      parent::setView($view, $viewParams);
      $this->main->hooks->run('controller:set-view', [$this, $view, $viewParams]);
    }
  }

  public function getBreadcrumbs(): array
  {
    return [];
  }

}
