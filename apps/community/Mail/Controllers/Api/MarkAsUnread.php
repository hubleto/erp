<?php

namespace HubletoApp\Community\Mail\Controllers\Api;

class MarkAsUnread extends \HubletoMain\Core\Controllers\Controller {

  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    $idMail = $this->main->urlParamAsInteger('idMail');
    $mMail = new \HubletoApp\Community\Mail\Models\Mail($this->main);
    $mMail->record->find($idMail)->update(['read' => null]);
    return ['success' => true];
  }

}