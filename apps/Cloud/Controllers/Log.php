<?php

namespace Hubleto\App\Community\Cloud\Controllers;

class Log extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $mLog = $this->getModel(\Hubleto\App\Community\Cloud\Models\Log::class);
    $this->viewParams['log'] = $mLog->record->orderBy('log_datetime', 'asc')->get()?->toArray();

    $this->setView('@Hubleto:App:Community:Cloud/Log.twig');
  }

}
