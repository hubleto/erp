<?php

namespace HubletoApp\Community\Premium\Controllers;

class Premium extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    $this->hubletoApp->updatePremiumInfo();

    $currentCredit = $this->hubletoApp->recalculateCredit();
    $this->viewParams['currentCredit'] = $currentCredit;

    $this->viewParams['usageInfo'] = $this->hubletoApp->getPremiumInfo();

    $mLog = new \HubletoApp\Community\Premium\Models\Log($this->main);
    $this->viewParams['log'] = $mLog->record->orderBy('date', 'desc')->get()->toArray();

    $this->setView('@HubletoApp:Community:Premium/Premium.twig');
  }

}