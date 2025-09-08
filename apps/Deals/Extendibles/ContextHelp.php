<?php

namespace Hubleto\App\Community\Deals\Extendibles;

class ContextHelp extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      'deals' => [
        'en' => 'en/apps/community/deals',
      ],
    ];
  }

}