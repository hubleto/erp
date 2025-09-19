<?php

namespace Hubleto\App\Community\Leads\Controllers\Api;

use Exception;
use Hubleto\App\Community\Leads\Models\Lead;

class SaveBulkStatusChange extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {

    $records = $this->router()->urlParamAsArray("record");

    /** @var Lead */
    $mLead = $this->getModel(Lead::class);

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
