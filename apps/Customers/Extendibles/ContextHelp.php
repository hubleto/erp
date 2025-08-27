<?php

namespace HubletoApp\Community\Customers\Extendibles;

class ContextHelp extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      'customers' => [
        'en' => 'en/apps/community/customers',
      ],
    ];
  }

}