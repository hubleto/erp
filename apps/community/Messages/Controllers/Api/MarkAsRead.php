<?php

namespace HubletoApp\Community\Messages\Controllers\Api;

class MarkAsRead extends \HubletoMain\Core\Controllers\Controller {

  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    $idMessage = $this->main->urlParamAsInteger('idMessage');
    $mMessage = new \HubletoApp\Community\Messages\Models\Message($this->main);
    $mMessage->record->find($idMessage)->update(['read' => date('Y-m-d H:i:s')]);
    return ['success' => true];
  }

}