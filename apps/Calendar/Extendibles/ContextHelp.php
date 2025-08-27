<?php

namespace HubletoApp\Community\Calendar\Extendibles;

class ContextHelp extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      'calendar' => [
        'en' => 'en/apps/community/calendar',
      ],
    ];
  }

}