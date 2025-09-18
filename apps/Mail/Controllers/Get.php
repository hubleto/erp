<?php

namespace Hubleto\App\Community\Mail\Controllers;

use Hubleto\App\Community\Mail\Mailer;

class Get extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'mail', 'content' => $this->translate('Mail') ],
      [ 'url' => 'mail', 'content' => $this->translate('Get') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $mailer = $this->getService(Mailer::class);
    $result = $mailer->getMails();

    $this->viewParams['result'] = $result;

    $this->setView('@Hubleto:App:Community:Mail/Get.twig');
  }

}
