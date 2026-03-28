<?php

namespace Hubleto\App\Community\Tasks\Controllers\Api;

use Hubleto\App\Community\Mail\Models\Mail;
use Hubleto\App\Community\Tasks\Models\Task;
use Hubleto\Erp\Controllers\ApiController;

class CreateFromMail extends ApiController
{

  public function renderJson(): array
  {

    $idMail = $this->router()->urlParamAsInteger('idMail');

    /** @var Mail */
    $mMail = $this->getModel(Mail::class);

    /** @var Task */
    $mTask = $this->getModel(Task::class);

    if ($idMail > 0) {
      $mail = $mMail->record->prepareReadQuery()->where($mMail->table . '.id', $idMail)->first();
      if ($mail->id) {
        $idTask = $mTask->record->recordCreate([
          'title' => $mail->subject,
          'description' => $mail->body_text,
          'id_developer' => $this->authProvider()->getUserId(),
          'date_start' => date("Y-m-d")
        ])['id'];
      }
    }

    return ['status' => 'success', 'idTask' => $idTask];
  }

}
