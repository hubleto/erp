<?php

namespace HubletoApp\Community\Leads\Controllers\Api;

use HubletoApp\Community\Leads\Models\Lead;
use HubletoApp\Community\Leads\Models\LeadActivity;

class LogActivity extends \HubletoMain\Core\Controllers\ApiController
{

  public function renderJson(): ?array
  {
    $idLead = $this->main->urlParamAsInteger("idLead");
    $activity = $this->main->urlParamAsString("activity");
    if ($idLead > 0 && $activity != '') {
      $mLead = new Lead($this->main);
      $lead = $mLead->record->find($idLead)->first()?->toArray();

      if ($lead && $lead['id'] > 0) {
        $mLeadActivity = new LeadActivity($this->main);
        $mLeadActivity->record->recordCreate([
          'id_lead' => $idLead,
          'subject' => $activity,
          'date_start' => date('Y-m-d'),
          'time_start' => date('H:i:s'),
          'all_day' => true,
          'completed' => true,
          'id_owner' => $this->main->auth->getUserId(),
        ]);
      }
    }

    return [
      "status" => "success",
      "idLead" => $idLead,
    ];
  }

}
