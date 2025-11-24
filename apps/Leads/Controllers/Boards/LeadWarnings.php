<?php

namespace Hubleto\App\Community\Leads\Controllers\Boards;

use Hubleto\App\Community\Leads\Models\Lead;

class LeadWarnings extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $warningsTotal = 0;

    /** @var Lead */
    $mLead = $this->getModel(Lead::class);

    $myLeads = $mLead->record->prepareReadQuery()
      ->where($mLead->table . ".is_archived", 0)
      ->where($mLead->table . ".is_closed", 0)
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
      if ($futureActivities == 0) {
        $items[] = $lead;
        $warningsTotal++;
      }
    }

    $warnings['open-leads-without-future-plan'] = [
      "title" => $this->translate('Open leads without future plan'),
      "titleCssClass" => "bg-red-400 p-2 text-white",
      "items" => $items,
    ];
    //

    $this->viewParams['warningsTotal'] = $warningsTotal;
    $this->viewParams['warnings'] = $warnings;

    $this->setView('@Hubleto:App:Community:Leads/Boards/LeadWarnings.twig');
  }

}
