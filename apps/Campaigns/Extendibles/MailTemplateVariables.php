<?php

namespace Hubleto\App\Community\Campaigns\Extendibles;

class MailTemplateVariables extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      'campaign.utmSource',
      'campaign.utmCampaign',
      'campaign.utmTerm',
      'campaign.utmContent',
    ];
  }

}