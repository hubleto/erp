<?php

namespace HubletoApp\Community\Leads\Controllers\Api;

use Exception;
use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Deals\Models\DealDocument;
use HubletoApp\Community\Deals\Models\DealHistory;
use HubletoApp\Community\Deals\Models\DealProduct;
use HubletoApp\Community\Leads\Models\Lead;
use HubletoApp\Community\Leads\Models\LeadDocument;
use HubletoApp\Community\Leads\Models\LeadHistory;
use HubletoApp\Community\Leads\Models\LeadProduct;
use HubletoApp\Community\Pipeline\Models\PipelineStep;
use HubletoApp\Community\Settings\Models\Setting;

class ConvertLead extends \HubletoMain\Core\Controllers\Controller
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
    $mLeadProduct = new LeadProduct($this->main);
    $mLeadDocument = new LeadDocument($this->main);

    $mDeal = new Deal($this->main);
    $mDealHistory = new DealHistory($this->main);
    $mDealProduct = new DealProduct($this->main);
    $mDealDocument = new DealDocument($this->main);
    $deal = null;

    $mSettings = new Setting($this->main);
    $mPipepelineStep = new PipelineStep($this->main);
    $defaultPipeline =(int) $mSettings->record
      ->where("key", "Apps\Community\Pipeline\DefaultPipeline")
      ->first()
      ->value
    ;
    $defaultPipelineFirstStep =(int) $mPipepelineStep->record
      ->where("id_pipeline", $defaultPipeline)
      ->orderBy("id", "asc")
      ->first()
      ->id
    ;

    try {
      $lead = $mLead->record->where("id", $leadId)->first();

      $deal = $mDeal->record->recordCreate([
        "identifier" => $lead->identifier,
        "title" => $lead->title,
        "id_customer" => $lead->id_customer,
        "id_contact" => $lead->id_contact,
        "price" => $lead->price,
        "id_currency" => $lead->id_currency,
        "date_expected_close" => $lead->date_expected_close,
        "date_created" => date("Y-m-d"),
        "id_user" => $lead->id_user,
        "source_channel" => $lead->source_channel,
        "is_archived" => $lead->is_archived,
        "id_lead" => $lead->id,
        "deal_result" => 3,
        "id_pipeline" => $defaultPipeline ?? null,
        "id_pipeline_step" => $defaultPipelineFirstStep ?? null,
      ]);

      $lead->status = $mLead::STATUS_COMPLETED;
      $lead->save();

      $leadProducts = $mLeadProduct->record->where("id_lead", $leadId)->get();

      foreach ($leadProducts as $leadProduct) { //@phpstan-ignore-line
        $mDealProduct->record->recordCreate([
          "id_service" => $leadProduct->id_service,
          "id_deal" => $deal['id'],
          "unit_price" => $leadProduct->unit_price,
          "amount" => $leadProduct->amount,
          "discount" => $leadProduct->discount,
          "vat" => $leadProduct->vat,
        ]);
      }

      $leadDocuments = $mLeadDocument->record->where("id_lookup", $leadId)->get();

      foreach ($leadDocuments as $leadDocument) { //@phpstan-ignore-line
        $mDealDocument->record->recordCreate([
          "id_document" => $leadDocument->id_document,
          "id_deal" => $deal['id']
        ]);
      }

      $leadHistories = $mLeadHistory->record->where("id_lead", $leadId)->get();

      foreach ($leadHistories as $leadHistory) { //@phpstan-ignore-line
        $mDealHistory->record->recordCreate([
          "description" => $leadHistory->description,
          "change_date" => $leadHistory->change_date,
          "id_deal" => $deal['id']
        ]);
      }

      $mLeadHistory->record->recordCreate([
        "description" => "Converted to a Deal",
        "change_date" => date("Y-m-d"),
        "id_lead" => $leadId
      ]);

      $mDealHistory->record->recordCreate([
        "description" => "Converted to a Deal",
        "change_date" => date("Y-m-d"),
        "id_deal" => $deal['id']
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
      "idDeal" => $deal['id'],
      "title" => str_replace(" ", "+", (string) $deal['title'])
    ];
  }

}
