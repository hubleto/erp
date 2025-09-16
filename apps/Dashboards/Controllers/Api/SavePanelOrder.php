<?php

namespace Hubleto\App\Community\Dashboards\Controllers\Api;

use Hubleto\App\Community\Dashboards\Models\Panel;

class SavePanelOrder extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idDashboard = $this->router()->urlParamAsInteger("idDashboard");
    $panelOrder = $this->router()->urlParamAsArray("panelOrder");

    $mPanel = $this->getModel(Panel::class);

    $order = 1;
    foreach ($panelOrder as $idPanel) {
      $mPanel->record->where('id_dashboard', $idDashboard)->where('id', $idPanel)
        ->update(['order' => $order++])
      ;
    }

    return [];
  }
}
