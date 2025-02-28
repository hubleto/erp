<?php

namespace HubletoMain\Core;

use \ADIOS\Core\Helper;

class Controller extends \ADIOS\Core\Controller
{

  public \HubletoMain $main;

  function __construct(\HubletoMain $main)
  {
    $this->main = $main;

    $reflection = new \ReflectionClass($this);
    preg_match('/^(.*?)\\\Controllers\\\(.*?)$/', $reflection->getName(), $m);
    if (isset($m[1]) && isset($m[2])) {
      $this->translationContext = $m[1] . '\\Loader::Controllers\\' . $m[2];
    }

    parent::__construct($main);

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

    $logDir = $this->app->config->getAsString('logDir');

    if ($this->main->auth->isUserInSession()) {
      $user = $this->main->auth->getUserFromSession();

      if (!empty($logDir) && is_dir($logDir)) {
        if (!is_dir($logDir . '/usage')) mkdir($logDir . '/usage');
        file_put_contents(
          $logDir . '/usage/' . date('Y-m-d') . '.log',
          date('H:i:s') . ' ' . $user['id'] . ' ' . get_class($this) . ' '. json_encode(array_keys($this->main->getUrlParams()), true) . "\n",
          FILE_APPEND
        );
      }
    }

    parent::prepareView();

    $this->viewParams['main'] = $this->main;
    $this->viewParams['help'] = $this->main->help;
    $this->viewParams['breadcrumbs'] = $this->getBreadcrumbs();
    $this->viewParams['requestedUri'] = $this->main->requestedUri;

    $appsInSidebar = $this->main->appManager->getRegisteredApps();

    foreach ($appsInSidebar as $appNamespace => $app) {
      if ($app->configAsInteger('sidebarOrder') <= 0) {
        unset($appsInSidebar[$appNamespace]);
      }

      if (str_starts_with($this->main->requestedUri, $app->manifest['rootUrlSlug'])) {
        $appsInSidebar[$appNamespace]->isActivated = true;
      }
    }

    uasort($appsInSidebar, function($a, $b) {
      $aOrder = $a->configAsInteger('sidebarOrder');
      $bOrder = $b->configAsInteger('sidebarOrder');
      return $aOrder <=> $bOrder;
    });

    $this->viewParams['appsInSidebar'] = $appsInSidebar;

    $tmp = strpos($this->main->requestedUri, '/');
    if ($tmp === false) $this->viewParams['requestedUriFirstPart'] = $this->main->requestedUri;
    else $this->viewParams['requestedUriFirstPart'] = substr($this->main->requestedUri, 0, (int) $tmp);
  }

  public function getBreadcrumbs(): array
  {
    return [];
  }

}
