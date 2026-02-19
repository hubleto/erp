<?php

namespace Hubleto\App\Community\Orders\Controllers;

class Quotes extends \Hubleto\Erp\Controller
{

  /**
   * Returns parameters for the view to be rendered, as well as the
   * path to the view. For more information about controllers see
   * https://developer.hubleto.com/v0/docs/controllers
   *
   * @return void
   * 
   */
  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:Orders/Quotes.twig');
  }

}
