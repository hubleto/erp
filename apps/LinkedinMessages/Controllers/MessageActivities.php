<?php

namespace Hubleto\App\Community\LinkedinMessages\Controllers;

class MessageActivities extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'linkedin-messages', 'content' => $this->translate('LinkedIn messages') ],
      [ 'url' => '', 'content' => $this->translate('Follow-ups') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Custom:LinkedinMessages/MessageActivities.twig');
  }
}
