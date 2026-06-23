<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers;

class EmailClicks extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Emails') ],
      [ 'url' => 'emails/clicks', 'content' => $this->translate('Clicks') ]
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:EmailMarketing/EmailClicks.twig');
  }

}
