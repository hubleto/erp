<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers\Api;

use Hubleto\App\Community\EmailMarketing\Models\EmailRecipient;

class RemoveAllRecipients extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idEmail = $this->router()->urlParamAsInteger('idEmail');

    /** @var EmailRecipient */
    $mEmailRecipient = $this->getModel(EmailRecipient::class);

    try {

      $deleted = $mEmailRecipient->record->where('id_email', $idEmail)->delete();

      return ['status' => 'success', 'deleted' => $deleted];

    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }
  }
}
