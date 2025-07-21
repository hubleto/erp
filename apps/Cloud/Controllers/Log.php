<?php

namespace HubletoApp\Community\Cloud\Controllers;

class Log extends \HubletoMain\Core\Controllers\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $mLog = $this->main->di->create(\HubletoApp\Community\Cloud\Models\Log::class);
    $this->viewParams['log'] = $mLog->record->orderBy('log_datetime', 'asc')->get()?->toArray();

    $this->setView('@HubletoApp:Community:Cloud/Log.twig');
  }

}
