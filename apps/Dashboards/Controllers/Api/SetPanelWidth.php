<?php

namespace Hubleto\App\Community\Dashboards\Controllers\Api;

use Hubleto\App\Community\Dashboards\Models\Panel;

class SetPanelWidth extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idDashboard = $this->router()->urlParamAsInteger("idDashboard");
    $idPanel = $this->router()->urlParamAsInteger("idPanel");
    $width = $this->router()->urlParamAsInteger("width");

    $mPanel = $this->getModel(Panel::class);

    $mPanel->record->where('id_dashboard', $idDashboard)->where('id', $idPanel)
      ->update(['width' => $width])
    ;

    return [];
  }
}
