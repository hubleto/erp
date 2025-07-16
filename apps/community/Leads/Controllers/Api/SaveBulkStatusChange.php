<?php

namespace HubletoApp\Community\Leads\Controllers\Api;

use Exception;
use HubletoApp\Community\Leads\Models\Lead;

class SaveBulkStatusChange extends \HubletoMain\Core\Controllers\ApiController
{

  public function renderJson(): ?array
  {

    $records = $this->main->urlParamAsArray("record");
    $mLead = new Lead($this->main);

    try {
      foreach ($records as $key => $lead) {
        $mLead->record
          ->where("id", (int)$lead["id"])
          ->update(["status" => (int)$lead["status"]])
        ;
      }
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "status" => "success",
    ];
  }

}
