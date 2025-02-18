<?php

namespace HubletoMain\Core;

use \ADIOS\Core\Helper;

class Controller extends \ADIOS\Core\Controller
{

  public \HubletoMain $main;

  function __construct(\HubletoMain $main)
  {
    $this->main = $main;
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
    parent::prepareView();

    $this->viewParams['main'] = $this->main;
    $this->viewParams['help'] = $this->main->help;
    $this->viewParams['breadcrumbs'] = $this->getBreadcrumbs();
    $this->viewParams['requestedUri'] = $this->main->requestedUri;

    $appsInSidebar = $this->main->appManager->getRegisteredApps();
    foreach ($appsInSidebar as $appClass => $app) {
      if ($app->configAsInteger('sidebarOrder') <= 0) {
        unset($appsInSidebar[$appClass]);
      }
    }
    uasort($appsInSidebar, function($a, $b) {
      $aOrder = $a->configAsInteger('sidebarOrder');
      $bOrder = $b->configAsInteger('sidebarOrder');
      return $aOrder <=> $bOrder;
    });

    $this->viewParams['appsInSidebar'] = $appsInSidebar;

    $this->viewParams['activatedApp'] = $this->main->appManager->getActivatedApp();
    if ($this->viewParams['activatedApp']) {
      $this->viewParams['activatedApppSidebar'] = $this->viewParams['activatedApp']->sidebar->getItems();
    }

    $tmp = strpos($this->main->requestedUri, '/');
    if ($tmp === false) $this->viewParams['requestedUriFirstPart'] = $this->main->requestedUri;
    else $this->viewParams['requestedUriFirstPart'] = substr($this->main->requestedUri, 0, (int) $tmp);

    // $this->viewParams['sidebar'] = [
    //   'level1Items' => $this->main->getSidebar()->getItems(1),
    //   'level2Items' => $this->main->getSidebar()->getItems(2),
    // ];
  }

  public function getBreadcrumbs(): array
  {
    return [];
    //   [ 'url' => '', 'content' => '<i class="fas fa-home"></i>' ]
    // ];
  }

}
