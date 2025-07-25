<?php

namespace HubletoApp\Community\Leads\Controllers\Boards;

use HubletoApp\Community\Leads\Models\Lead;

class LeadValueByScore extends \HubletoMain\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $mLead = $this->main->di->create(Lead::class);

    $leads = $mLead->record
      ->selectRaw("score, SUM(price) as price")
      ->where("is_archived", 0)
      ->where("id_owner", $this->main->auth->getUserId())
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

    $this->setView('@HubletoApp:Community:Leads/Boards/LeadValueByScore.twig');
  }

}
