<?php

namespace Hubleto\App\Community\Dashboards\Controllers\Api;

use Hubleto\App\Community\Dashboards\Models\Panel;

class SortPanels extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idDashboard = $this->router()->urlParamAsInteger("idDashboard");
    $idPanelsSorted = $this->router()->urlParamAsArray("idPanelsSorted");

    $mPanel = $this->getModel(Panel::class);

    $position = 1;
    foreach ($idPanelsSorted as $idPanel) {
      $mPanel->record->where('id_dashboard', $idDashboard)->where('id', $idPanel)
        ->update(['position' => $position++])
      ;
    }

    return [];
  }
}
