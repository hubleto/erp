<?php

namespace Hubleto\App\Community\Leads\Controllers\Api;

use Exception;
use Hubleto\App\Community\Deals\Models\Deal;
use Hubleto\App\Community\Deals\Models\DealDocument;
use Hubleto\App\Community\Deals\Models\DealHistory;
use Hubleto\App\Community\Deals\Models\DealProduct;
use Hubleto\App\Community\Leads\Models\Lead;
use Hubleto\App\Community\Leads\Models\LeadDocument;
use Hubleto\App\Community\Leads\Models\LeadHistory;
use Hubleto\App\Community\Leads\Models\LeadProduct;
use Hubleto\App\Community\Pipeline\Models\PipelineStep;
use Hubleto\App\Community\Settings\Models\Setting;

class MoveToArchive extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    if (!$this->getRouter()->isUrlParam("recordId")) {
      return [
        "status" => "failed",
        "error" => "The lead for converting was not set"
      ];
    }

    $leadId = $this->getRouter()->urlParamAsInteger("recordId");
    $mLead = $this->getService(Lead::class);
    $mLead->record->find($leadId)->update(['is_archived' => true]);

    return [
      "status" => "success",
      "idLead" => $leadId,
    ];
  }

}
