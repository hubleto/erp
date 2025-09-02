<?php

namespace Hubleto\App\Community\Cloud\Controllers\Api;

class AcceptLegalDocuments extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $this->config()->saveForUser('legalDocumentsAccepted', date('Y-m-d H:i:s'));
    $this->router()->redirectTo('');
  }

}
