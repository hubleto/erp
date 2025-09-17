<?php

namespace Hubleto\App\Community\Leads\Controllers\Boards;


use Hubleto\App\Community\Leads\Models\Lead;

class LeadValueByScore extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $mLead = $this->getService(Lead::class);

    $leads = $mLead->record
      ->selectRaw("score, SUM(price) as price")
      ->where("is_archived", 0)
      ->where("id_owner", $this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())
      ->with('CURRENCY')
      ->groupBy('score')
      ->get()
      ->toArray()
    ;

    $chartData = [
      'labels' => [],
      'values' => [],
      // 'colors' => [],
    ];

    $minScore = null;
    $maxScore = null;
    foreach ($leads as $lead) {
      $maxScore = $maxScore === null ? $lead['score'] : max($maxScore, $lead['score']);
      $minScore = $minScore === null ? $lead['score'] : min($minScore, $lead['score']);
    }

    foreach ($leads as $lead) {
      $chartData['labels'][] = 'Score ' . $lead['score'];
      $chartData['values'][] = $lead['price'];

      $scoreNormalized = $lead['score'] / $maxScore;

      $chartData['colors'][] = 'rgb('
        . (40 + $scoreNormalized * 160)
        . ', ' . (120 + $scoreNormalized * 60)
        . ', ' . (70 + $scoreNormalized * 160)
        . ')'
      ;
    }

    $this->viewParams['chartData'] = $chartData;

    $this->setView('@Hubleto:App:Community:Leads/Boards/LeadValueByScore.twig');
  }

}
