<?php

namespace Hubleto\App\Community\Deals\Controllers;


use Hubleto\App\Community\Deals\Models\DealActivity;

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

    /** @var DealActivity */
    $mDealActivity = $this->getModel(DealActivity::class);

    $futureActivities = $mDealActivity->record
      ->prepareReadQuery()
      ->with('DEAL')
      ->where('date_start', '>=', date('Y-m-d'))
      ->where('completed', false)
      ->orderBy('date_start')
      ->orderBy('time_start')
      ->get()
    ;

    $this->viewParams['futureActivities'] = $futureActivities;

    $this->setView('@Hubleto:App:Community:Deals/Plan.twig');
  }

}
