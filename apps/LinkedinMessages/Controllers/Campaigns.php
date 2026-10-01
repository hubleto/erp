<?php

namespace Hubleto\App\Custom\LinkedinMessages\Controllers;

class Campaigns extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'linkedin-messages', 'content' => $this->translate('LinkedIn messages') ],
      [ 'url' => '', 'content' => $this->translate('Campaigns') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Custom:LinkedinMessages/Campaigns.twig');
  }
}
