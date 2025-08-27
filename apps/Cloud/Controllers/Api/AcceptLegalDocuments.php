<?php

namespace Hubleto\App\Community\Cloud\Controllers\Api;

class AcceptLegalDocuments extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $this->getConfig()->saveForUser('legalDocumentsAccepted', date('Y-m-d H:i:s'));
    $this->getRouter()->redirectTo('');
  }

}
