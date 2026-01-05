<?php

namespace Hubleto\App\Community\Cloud\Controllers\Api;

class GetPartnerInfo extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $partnerUid = $this->config()->getAsString('partnerUid');
    $partnerInfo = [];

    if (!empty($partnerUid)) {
      $partnerInfoStr = \Hubleto\Framework\Helper::loadUrl("https://partners.hubleto.com/" . $partnerUid . ".json");
      try {
        $partnerInfo = @json_decode($partnerInfoStr, true);
        if (!is_array($partnerInfo)) $partnerInfo = [];
      } catch (\Throwable) {
        $partnerInfo = [];
      }
    }

    return $partnerInfo;
  }

}
