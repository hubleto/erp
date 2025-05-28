<?php

namespace HubletoApp\Community\Cloud\Controllers;

class AcceptLegalDocuments extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    $this->hubletoApp->saveConfigForUser('legalDocumentsAccepted', date('Y-m-d H:i:s'));
    $this->main->router->redirectTo('');
  }

}