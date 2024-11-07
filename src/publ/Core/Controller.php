<?php

namespace CeremonyCrmApp\Core;

use \ADIOS\Core\Helper;

class Controller extends \ADIOS\Core\Controller
{

  /**
    * Executed after the init() phase.
    * Validates inputs ($this->params) used for the TWIG template.
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
   * @throws Exception Should throw an exception on error.
   */
  public function init()
  {
    // Put your controller's initialization code here. See example below.
    // Throw an exception on error.

    if (!$this->validateInputs()) {
      throw new \Exception("Malformed URL");
    }
  }

  /**
   * Returns parameters used to render TWIG template.
   *
   * @return array Parameters used to render TWIG template.
   */
  public function prepareViewParams()
  {
    parent::prepareViewParams();

    $this->viewParams['breadcrumbs'] = $this->getBreadcrumbs();
    $this->viewParams['requestedUri'] = $this->app->requestedUri;

    $tmp =  strpos($this->app->requestedUri, '/');
    if ($tmp === false) $this->viewParams['requestedUriFirstPart'] = $this->app->requestedUri;
    else $this->viewParams['requestedUriFirstPart'] = substr($this->app->requestedUri, 0, strpos($this->app->requestedUri, '/'));

    $this->viewParams['sidebar'] = [
      'level1Items' => $this->app->getSidebar()->getItems(1),
      'level2Items' => $this->app->getSidebar()->getItems(2),
    ];
  }

  public function getBreadcrumbs(): array
  {
    return [];
    //   [ 'url' => '', 'content' => '<i class="fas fa-home"></i>' ]
    // ];
  }

}
