<?php

namespace Hubleto\App\Community\EmailMarketing\Extendibles;

class MailTemplateVariables extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      'email-marketing.utmSource',
      'email-marketing.utmCampaign',
      'email-marketing.utmTerm',
      'email-marketing.utmContent',
    ];
  }

}