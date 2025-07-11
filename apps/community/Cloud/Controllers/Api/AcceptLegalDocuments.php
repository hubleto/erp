<?php

namespace HubletoApp\Community\Cloud\Controllers\Api;

class AcceptLegalDocuments extends \HubletoMain\Core\Controllers\Controller {

  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;
  public bool $permittedForAllUsers = true;

  public function renderJson(): ?array
  {
    $this->hubletoApp->saveConfigForUser('legalDocumentsAccepted', date('Y-m-d H:i:s'));
    $this->main->router->redirectTo('');
  }

}