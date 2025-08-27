<?php

namespace HubletoApp\Community\Leads\Controllers\Api;

use Exception;
use HubletoApp\Community\Leads\Models\Lead;

class SaveBulkStatusChange extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {

    $records = $this->getRouter()->urlParamAsArray("record");
    $mLead = $this->getService(Lead::class);

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
