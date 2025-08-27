<?php

namespace Hubleto\App\Community\Leads\Extendibles;

class ContextHelp extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      'leads' => [
        'en' => 'en/apps/community/leads',
      ],
    ];
  }

}