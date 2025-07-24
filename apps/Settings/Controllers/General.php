<?php

namespace HubletoApp\Community\Settings\Controllers;

class General extends \Hubleto\Framework\Controllers\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Settings/General.twig');
  }

}
