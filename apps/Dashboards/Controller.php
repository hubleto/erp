<?php

namespace Hubleto\App\Community\Dashboards;

class Controller extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $this->viewParams['idPanel'] = $this->router()->urlParamAsInteger('idPanel');
    $this->viewParams['configuration'] = $this->router()->urlParamAsArray('configuration');
  }
}