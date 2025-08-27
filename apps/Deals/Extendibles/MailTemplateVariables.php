<?php

namespace HubletoApp\Community\Deals\Extendibles;

class MailTemplateVariables extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      'deal.identifier',
      'deal.price_excl_vat',
    ];
  }

}