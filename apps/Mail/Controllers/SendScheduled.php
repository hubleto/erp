<?php

namespace Hubleto\App\Community\Mail\Controllers;

use Hubleto\App\Community\Mail\Crons\SendMails;

class SendScheduled extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'scheduled', 'content' => $this->translate('Scheduled') ],
    ]);
  }

  public function prepareView(): void
  {
    $maxMailsToSend = $this->router()->urlParamAsInteger('maxMailsToSend', 3);
    if ($maxMailsToSend > 30) $maxMailsToSend = 30;

    parent::prepareView();

    $this->logger()->clearLogCache();
    $sendMailsCron = $this->getService(SendMails::class);
    $sendMailsCron->maxMailsToSend = $maxMailsToSend;
    $sendMailsCron->run();
    $this->viewParams['log'] = $this->logger()->getLogCache();

    $this->setView('@Hubleto:App:Community:Mail/SendScheduled.twig');
  }

}
