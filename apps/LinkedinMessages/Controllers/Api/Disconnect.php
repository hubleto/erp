<?php

namespace Hubleto\App\Custom\LinkedinMessages\Controllers\Api;

use Hubleto\App\Custom\LinkedinMessages\Models\Account;

class Disconnect extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idAccount = $this->router()->urlParamAsInteger('idAccount');

    /** @var Account */
    $mAccount = $this->getModel(Account::class);

    // reading through prepareReadQuery() respects the visibility rules of the current user
    $account = $mAccount->record->prepareReadQuery()->where($mAccount->table . '.id', $idAccount)->first()?->toArray();
    if (!$account) {
      return [ 'status' => 'error', 'message' => $this->translate('Account not found.') ];
    }

    $mAccount->record->where('id', $idAccount)->update([
      'access_token' => null,
      'refresh_token' => null,
      'token_expires_at' => null,
      'status' => Account::STATUS_ERROR,
      'last_sync_info' => $this->translate('Disconnected.'),
    ]);

    return [ 'status' => 'success', 'idAccount' => $idAccount ];
  }

}
