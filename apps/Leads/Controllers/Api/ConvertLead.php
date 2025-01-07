<?php

namespace HubletoApp\Leads\Controllers\Api;

use HubletoApp\Settings\Models\Setting;
use HubletoApp\Deals\Models\Deal;
use HubletoApp\Deals\Models\DealDocument;
use HubletoApp\Deals\Models\DealHistory;
use HubletoApp\Deals\Models\DealService;
use HubletoApp\Leads\Models\Lead;
use HubletoApp\Leads\Models\LeadDocument;
use HubletoApp\Leads\Models\LeadHistory;
use HubletoApp\Leads\Models\LeadService;
use Exception;

class ConvertLead extends \HubletoCore\Core\Controller
{

  public function renderJson(): ?array
  {
    $leadId = $this->app->params["recordId"];

    $mLead = new Lead($this->app);
    $mLeadHistory = new LeadHistory($this->app);
    $mLeadService = new LeadService($this->app);
    $mLeadDocument = new LeadDocument($this->app);

    $mDeal = new Deal($this->app);
    $mDealHistory = new DealHistory($this->app);
    $mDealService = new DealService($this->app);
    $mDealDocument = new DealDocument($this->app);
    $deal = null;

    $mSettings = new Setting($this->app);
    $defaultPipeline =(int) $mSettings->eloquent
      ->where("key", "Modules\Core\Settings\Pipeline\DefaultPipeline")
      ->first()
      ->value
    ;

    try {
      $lead = $mLead->eloquent->where("id", $leadId)->first();

      $deal = $mDeal->eloquent->create([
        "title" => $lead->title,
        "id_company" => $lead->id_company,
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

      foreach ($leadServices as $leadService) {
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
      foreach ($leadDocuments as $leadDocument) {
        $mDealDocument->eloquent->create([
          "id_document" => $leadDocument->id_document,
          "id_deal" => $deal->id
        ]);
      }

      $leadHistories = $mLeadHistory->eloquent->where("id_lead", $leadId)->get();

      foreach ($leadHistories as $leadHistory) {
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
      "title" => str_replace(" ", "+", $deal->title)
    ];
  }

}
