<?php

namespace Hubleto\App\Community\Leads\Controllers;


use Hubleto\App\Community\Leads\Models\LeadActivity;

class Plan extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Plan') ],
    ]);
  }

  public function prepareView(): void
  {

    parent::prepareView();

    /** @var LeadActivity */
    $mLeadActivity = $this->getModel(LeadActivity::class);

    $futureActivities = $mLeadActivity->record
      ->prepareReadQuery()
      ->with('LEAD')
      ->where('date_start', '>=', date('Y-m-d'))
      ->where('completed', false)
      ->orderBy('date_start')
      ->orderBy('time_start')
      ->get()
    ;

    $this->viewParams['futureActivities'] = $futureActivities;

    $this->setView('@Hubleto:App:Community:Leads/Plan.twig');
  }

}
