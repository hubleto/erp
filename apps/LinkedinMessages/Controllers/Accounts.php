<?php

namespace Hubleto\App\Custom\LinkedinMessages\Controllers;

class Accounts extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'linkedin-messages', 'content' => $this->translate('LinkedIn messages') ],
      [ 'url' => '', 'content' => $this->translate('Accounts') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Custom:LinkedinMessages/Accounts.twig');
  }
}
