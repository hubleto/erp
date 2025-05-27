<?php

namespace HubletoApp\Community\Premium\Controllers;

class Log extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    $mLog = new \HubletoApp\Community\Premium\Models\Log($this->main);
    $this->viewParams['log'] = $mLog->record->orderBy('date', 'asc')->get()?->toArray();

    $this->setView('@HubletoApp:Community:Premium/Log.twig');
  }

}