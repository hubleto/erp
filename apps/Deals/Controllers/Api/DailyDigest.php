<?php

namespace Hubleto\App\Community\Deals\Controllers\Api;

use Hubleto\App\Community\Auth\AuthProvider;

class DailyDigest extends \Hubleto\Erp\Controllers\ApiController
{
  public function response(): array
  {
    $digest = [];

    $mDeal = $this->getModel(\Hubleto\App\Community\Deals\Models\Deal::class);

    $myDeals = $mDeal->record->prepareReadQuery()
      ->where($mDeal->table . ".is_archived", 0)
      ->where($mDeal->table . ".is_closed", 0)
      ->where($mDeal->table . ".id_owner", $this->getService(AuthProvider::class)->getUserId())
      ->orderBy('price_excl_vat', 'desc')
      ->get()
      ->toArray()
    ;

    foreach ($myDeals as $deal) {
      $futureActivities = 0;
      foreach ($deal['ACTIVITIES'] as $activity) {
        if (strtotime($activity['date_start']) > time()) {
          $futureActivities++;
        }
      }

      if ($futureActivities == 0) {
        $digest[] = [
          'color' => '#d7b628',
          'category' => 'Missing plan',
          'text' => $deal['identifier'] . ' ' . $deal['title'],
          'url' => 'deals/' . $deal['id'],
          'description' => 'You should schedule further activity for this deal.',
        ];
      }
    }

    return $digest;
  }

}
