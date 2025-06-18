<?php

namespace HubletoApp\Community\Messages\Controllers\Api;

class MarkAsUnread extends \HubletoMain\Core\Controllers\Controller {

  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    $idMessage = $this->main->urlParamAsInteger('idMessage');
    $mMessage = new \HubletoApp\Community\Messages\Models\Message($this->main);
    $mMessage->record->find($idMessage)->update(['read' => null]);
    return ['success' => true];
  }

}