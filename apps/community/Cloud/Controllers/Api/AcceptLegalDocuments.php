<?php

namespace HubletoApp\Community\Cloud\Controllers\Api;

class AcceptLegalDocuments extends \HubletoMain\Core\Controllers\ApiController {

  public function renderJson(): ?array
  {
    $this->hubletoApp->saveConfigForUser('legalDocumentsAccepted', date('Y-m-d H:i:s'));
    $this->main->router->redirectTo('');
  }

}