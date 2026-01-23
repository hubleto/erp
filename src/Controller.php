<?php declare(strict_types=1);

namespace Hubleto\Erp;


use Hubleto\App\Community\Settings\PermissionsManager;
use Hubleto\Framework\Interfaces\AppManagerInterface;
use Hubleto\Framework\Config;
/**
 * @property \Hubleto\Erp\Loader $main
 */
class Controller extends \Hubleto\Framework\Controller
{

  public bool $disableLogUsage = false;
  public bool $permittedForAllUsers = false;

  public string $appNamespace = '';
  public null|\Hubleto\Framework\Interfaces\AppInterface $hubletoApp;

  public function __construct()
  {

    parent::__construct();

    $this->hubletoApp = $this->appManager()->getApp($this->appNamespace);
  }

  public function activeUserHasPermission(): bool
  {
    if (
      isset($this->hubletoApp)
      && $this->requiresAuthenticatedUser
      && !$this->permittedForAllUsers
      && !$this->getService(PermissionsManager::class)->isAppPermittedForActiveUser($this->hubletoApp)
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
    $this->eventManager()->fire('onControllerBeforeInit', [$this]);

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

    $this->eventManager()->fire('onControllerBeforePrepareView', [$this]);

    $logFolder = $this->config()->getAsString('logFolder');

    if ($this->getService(\Hubleto\Framework\AuthProvider::class)->isUserInSession()) {
      $user = $this->getService(\Hubleto\Framework\AuthProvider::class)->getUserFromSession();

      if (!empty($logFolder) && is_dir($logFolder)) {
        if (!is_dir($logFolder . '/usage')) {
          mkdir($logFolder . '/usage');
        }
        file_put_contents(
          $logFolder . '/usage/' . date('Y-m-d') . '.log',
          date('H:i:s') . ' ' . $user['id'] . ' ' . get_class($this) . ' '. json_encode(array_keys($this->router()->getUrlParams())) . "\n",
          FILE_APPEND
        );
      }
    }

    parent::prepareView();

    $this->viewParams['currentTheme'] = $this->config()->getAsString('uiTheme', 'default');

    if (isset($this->hubletoApp)) {
      $this->viewParams['app'] = $this->hubletoApp;
    }
    $this->viewParams['breadcrumbs'] = $this->getBreadcrumbs();
    $this->viewParams['requestedUri'] = $this->env()->requestedUri;

    $help = $this->getService(\Hubleto\App\Community\Help\Loader::class);
    $contextHelpUrls = $help->contextHelp[$this->router()->getRoute()] ?? '';

    $user = $this->getService(\Hubleto\Framework\AuthProvider::class)->getUser();

    if (isset($contextHelpUrls[$user['language']])) {
      $contextHelpUrl = $contextHelpUrls[$user['language']];
    } elseif (isset($contextHelpUrls['en'])) {
      $contextHelpUrl = $contextHelpUrls['en'];
    } else {
      $contextHelpUrl = '';
    }

    $this->viewParams['contextHelpUrl'] = $contextHelpUrl;

    $this->eventManager()->fire('onControllerAfterPrepareView', [$this]);

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
      $this->eventManager()->fire('onControllerSetView', [$this, $view]);
    }
  }

  public function getBreadcrumbs(): array
  {
    return [];
  }

}
