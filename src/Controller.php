<?php declare(strict_types=1);

namespace HubletoMain;

use Hubleto\Framework\Interfaces\AppManagerInterface;
use Hubleto\Framework\Config;
/**
 * @property \HubletoMain\Loader $main
 */
class Controller extends \Hubleto\Framework\Controller
{

  public bool $disableLogUsage = false;
  public bool $permittedForAllUsers = false;

  public string $appNamespace = '';
  public null|\Hubleto\Framework\Interfaces\AppInterface $hubletoApp;

  public function __construct()
  {

    $reflection = new \ReflectionClass($this);
    preg_match('/^(.*?)\\\Controllers\\\(.*?)$/', $reflection->getName(), $m);
    if (isset($m[1]) && isset($m[2])) {
      $this->appNamespace = $m[1];
      $this->translationContext = $m[1] . '\\Loader::Controllers\\' . $m[2];
    }

    parent::__construct();

    $this->hubletoApp = $this->getAppManager()->getApp($this->appNamespace);
  }

  public function activeUserHasPermission(): bool
  {
    if (
      isset($this->hubletoApp)
      && $this->requiresUserAuthentication
      && !$this->permittedForAllUsers
      && !$this->getPermissionsManager()->isAppPermittedForActiveUser($this->hubletoApp)
    ) {
      return false;
    }

    return true;
  }

  /**
    * Executed after the init() phase.
    * Validates inputs used for the TWIG template.
    *
    * return bool True if inputs are valid, otherwise false.
    */
  public function validateInputs(): bool
  {
    $valid = true;

    return $valid;

  }

  /**
   * Executed at the end of the constructor.
   *
   * @throws \Exception Should throw an exception on error.
   */
  public function init(): void
  {
    $this->getHookManager()->run('controller:init-start', [$this]);

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
    if (!$this->activeUserHasPermission()) {
      return;
    }

    $this->getHookManager()->run('controller:prepare-view-start', [$this]);

    $logFolder = $this->getConfig()->getAsString('logFolder');

    if ($this->getAuthProvider()->isUserInSession()) {
      $user = $this->getAuthProvider()->getUserFromSession();

      if (!empty($logFolder) && is_dir($logFolder)) {
        if (!is_dir($logFolder . '/usage')) {
          mkdir($logFolder . '/usage');
        }
        file_put_contents(
          $logFolder . '/usage/' . date('Y-m-d') . '.log',
          date('H:i:s') . ' ' . $user['id'] . ' ' . get_class($this) . ' '. json_encode(array_keys($this->getRouter()->getUrlParams())) . "\n",
          FILE_APPEND
        );
      }
    }

    parent::prepareView();

    $this->viewParams['currentTheme'] = $this->getConfig()->getAsString('uiTheme', 'default');

    if (isset($this->hubletoApp)) {
      $this->viewParams['app'] = $this->hubletoApp;
    }
    $this->viewParams['breadcrumbs'] = $this->getBreadcrumbs();
    $this->viewParams['requestedUri'] = $this->getEnv()->requestedUri;

    $help = $this->getService(\HubletoApp\Community\Help\Loader::class);
    $contextHelpUrls = $help->contextHelp[$this->getRouter()->getRoute()] ?? '';

    $user = $this->getAuthProvider()->getUser();

    if (isset($contextHelpUrls[$user['language']])) {
      $contextHelpUrl = $contextHelpUrls[$user['language']];
    } elseif (isset($contextHelpUrls['en'])) {
      $contextHelpUrl = $contextHelpUrls['en'];
    } else {
      $contextHelpUrl = '';
    }

    $this->viewParams['contextHelpUrl'] = $contextHelpUrl;

    $this->getHookManager()->run('controller:prepare-view-end', [$this]);

  }

  public function setView(string $view): void
  {
    if (!$this->activeUserHasPermission()) {
      $this->viewParams = [
        'message' => "You have no access neither to {$this->hubletoApp->manifest['name']} nor {$this->shortName}."
      ];
      parent::setView('@hubleto-main/AccessForbidden.twig');
    } else {
      parent::setView($view);
      $this->getHookManager()->run('controller:set-view', [$this, $view]);
    }
  }

  public function getBreadcrumbs(): array
  {
    return [];
  }

}
