<?php

namespace CeremonyCrmApp\Controllers;

use \ADIOS\Core\Helper;

class Desktop extends \CeremonyCrmApp\Core\Controller {

  /**
    * Executed after the init() phase.
    * Validates inputs ($this->params) used for the TWIG template.
    *
    * return bool True if inputs are valid, otherwise false.
    */
  public function validateInputs(): bool {
    return TRUE;
  }

  /**
   * Executed at the end of the constructor.
   *
   * @throws Exception Should throw an exception on error.
   */
  public function init() {
    // Put your controller's initialization code here. See example below.
    // Throw an exception on error.

    // if (!$this->validateInputs()) {
    //   throw new \ADIOS\Core\GeneralException("Inputs are invalid");
    // }
  }

  /**
   * Returns parameters used to render TWIG template.
   *
   * @return array Parameters used to render TWIG template.
   */
  // public function prepareViewParams() {
  //   parent::prepareViewParams();
  // }

}
