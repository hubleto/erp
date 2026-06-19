<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers\Api;

use Hubleto\App\Community\EmailMarketing\Models\Recipient;

class RemoveAllRecipients extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');
    $idEmail = $this->router()->urlParamAsInteger('idEmail');

    /** @var Recipient */
    $mRecipient = $this->getModel(Recipient::class);

    try {

      $query = $mRecipient->record;
      if ($idCampaign > 0) $query = $query->where('id_campaign', $idCampaign);
      if ($idEmail > 0) $query = $query->where('id_email', $idEmail);

      $deleted = $query->delete();

      return ['status' => 'success', 'deleted' => $deleted];

    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }
  }
}
