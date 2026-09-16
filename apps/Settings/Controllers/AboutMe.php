<?php

namespace Hubleto\App\Community\Settings\Controllers;

class AboutMe extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'about-me', 'content' => $this->translate('About me') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Settings/AboutMe.twig');
  }

}
