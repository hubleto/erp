<?php

namespace HubletoApp\Community\Cloud\Controllers;

class Log extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    $mLog = new \HubletoApp\Community\Cloud\Models\Log($this->main);
    $this->viewParams['log'] = $mLog->record->orderBy('date', 'asc')->get()?->toArray();

    $this->setView('@HubletoApp:Community:Cloud/Log.twig');
  }

}