<?php

namespace HubletoApp\Community\Cloud\Controllers;

class Log extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $mLog = $this->getModel(\HubletoApp\Community\Cloud\Models\Log::class);
    $this->viewParams['log'] = $mLog->record->orderBy('log_datetime', 'asc')->get()?->toArray();

    $this->setView('@HubletoApp:Community:Cloud/Log.twig');
  }

}
