<?php

namespace HubletoApp\Community\Leads\Controllers\Api;

use HubletoApp\Community\Settings\Models\Setting;
use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Deals\Models\DealDocument;
use HubletoApp\Community\Deals\Models\DealHistory;
use HubletoApp\Community\Deals\Models\DealService;
use HubletoApp\Community\Leads\Models\Lead;
use HubletoApp\Community\Leads\Models\LeadDocument;
use HubletoApp\Community\Leads\Models\LeadHistory;
use HubletoApp\Community\Leads\Models\LeadService;
use Exception;

class ConvertLead extends \HubletoMain\Core\Controller
{
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    if (!$this->main->isUrlParam("recordId")) {
      return [
        "status" => "failed",
        "error" => "The lead for converting was not set"
      ];
    }

    $leadId = $this->main->urlParamAsInteger("recordId");

    $mLead = new Lead($this->main);
    $mLeadHistory = new LeadHistory($this->main);
    $mLeadService = new LeadService($this->main);
    $mLeadDocument = new LeadDocument($this->main);

    $mDeal = new Deal($this->main);
    $mDealHistory = new DealHistory($this->main);
    $mDealService = new DealService($this->main);
    $mDealDocument = new DealDocument($this->main);
    $deal = null;

    $mSettings = new Setting($this->main);
    $defaultPipeline =(int) $mSettings->eloquent
      ->where("key", "Apps\Community\Settings\Pipeline\DefaultPipeline")
      ->first()
      ->value
    ;

    try {
      $lead = $mLead->eloquent->where("id", $leadId)->first();

      $deal = $mDeal->eloquent->create([
        "title" => $lead->title,
        "id_customer" => $lead->id_customer,
        "id_person" => $lead->id_person,
        "price" => $lead->price,
        "id_currency" => $lead->id_currency,
        "date_expected_close" => $lead->date_expected_close,
        "date_created" => date("Y-m-d"),
        "id_user" => $lead->id_user,
        "source_channel" => $lead->source_channel,
        "is_archived" => $lead->is_archived,
        "id_lead" => $lead->id,
        "id_pipeline" => $defaultPipeline,
        "id_pipeline_step" => null,
        "id_deal_status" => 1,
      ]);

      $leadServices = $mLeadService->eloquent->where("id_lead", $leadId)->get();

      foreach ($leadServices as $leadService) { //@phpstan-ignore-line
        $mDealService->eloquent->create([
          "id_service" => $leadService->id_service,
          "id_deal" => $deal->id,
          "unit_price" => $leadService->unit_price,
          "amount" => $leadService->amount,
          "discount" => $leadService->discount,
          "tax" => $leadService->tax,
        ]);
      }

      $leadDocuments = $mLeadDocument->eloquent->where("id_lead", $leadId)->get();

      foreach ($leadDocuments as $leadDocument) { //@phpstan-ignore-line
        $mDealDocument->eloquent->create([
          "id_document" => $leadDocument->id_document,
          "id_deal" => $deal->id
        ]);
      }

      $leadHistories = $mLeadHistory->eloquent->where("id_lead", $leadId)->get();

      foreach ($leadHistories as $leadHistory) { //@phpstan-ignore-line
        $mDealHistory->eloquent->create([
          "description" => $leadHistory->description,
          "change_date" => $leadHistory->change_date,
          "id_deal" => $deal->id
        ]);
      }

      $mLeadHistory->eloquent->create([
        "description" => "Converted to a Deal",
        "change_date" => date("Y-m-d"),
        "id_lead" => $leadId
      ]);

      $mDealHistory->eloquent->create([
        "description" => "Converted to a Deal",
        "change_date" => date("Y-m-d"),
        "id_deal" => $deal->id
      ]);

      $lead->is_archived = 1;
      $lead->save();
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "status" => "success",
      "idDeal" => $deal->id,
      "title" => str_replace(" ", "+", (string) $deal->title)
    ];
  }

}
