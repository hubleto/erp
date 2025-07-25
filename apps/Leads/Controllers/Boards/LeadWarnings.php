<?php

namespace HubletoApp\Community\Leads\Controllers\Boards;

use HubletoApp\Community\Leads\Models\Lead;

class LeadWarnings extends \HubletoMain\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $warningsTotal = 0;

    $mLead = $this->main->di->create(Lead::class);

    $myLeads = $mLead->record->prepareReadQuery()
      ->where($mLead->table . ".is_archived", 0)
      ->get()
      ->toArray()
    ;

    // open-leads-without-future-plan
    $items = [];

    foreach ($myLeads as $lead) {
      $futureActivities = 0;
      foreach ($lead['ACTIVITIES'] as $activity) {
        if (strtotime($activity['date_start']) > time()) {
          $futureActivities++;
        }
      }
      if (in_array($lead['status'], [Lead::STATUS_IN_PROGRESS]) && $futureActivities == 0) {
        $items[] = $lead;
        $warningsTotal++;
      }
    }

    $warnings['open-leads-without-future-plan'] = [
      "title" => $this->translate('Open leads without future plan'),
      "items" => $items,
    ];
    //

    $this->viewParams['warningsTotal'] = $warningsTotal;
    $this->viewParams['warnings'] = $warnings;

    $this->setView('@HubletoApp:Community:Leads/Boards/LeadWarnings.twig');
  }

}
