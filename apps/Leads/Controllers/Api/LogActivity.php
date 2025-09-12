<?php

namespace Hubleto\App\Community\Leads\Controllers\Api;

use Hubleto\App\Community\Leads\Models\Lead;
use Hubleto\App\Community\Leads\Models\LeadActivity;

class LogActivity extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idLead = $this->router()->urlParamAsInteger("idLead");
    $activity = $this->router()->urlParamAsString("activity");
    if ($idLead > 0 && $activity != '') {
      $mLead = $this->getService(Lead::class);
      $lead = $mLead->record->find($idLead)->first()?->toArray();

      if ($lead && $lead['id'] > 0) {
        $mLeadActivity = $this->getService(LeadActivity::class);
        $mLeadActivity->record->recordCreate([
          'id_lead' => $idLead,
          'subject' => $activity,
          'date_start' => date('Y-m-d'),
          'time_start' => date('H:i:s'),
          'all_day' => true,
          'completed' => true,
          'id_owner' => $this->getService(AuthProvider::class)->getUserId(),
        ]);
      }
    }

    return [
      "status" => "success",
      "idLead" => $idLead,
    ];
  }

}
