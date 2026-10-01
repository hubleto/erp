<?php

namespace Hubleto\App\Custom\LinkedinMessages\Controllers\Api;

use Hubleto\App\Custom\LinkedinMessages\Models\Account;
use Hubleto\App\Custom\LinkedinMessages\Sync as SyncService;

class Sync extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idAccount = $this->router()->urlParamAsInteger('idAccount');

    /** @var Account */
    $mAccount = $this->getModel(Account::class);
    /** @var \Hubleto\App\Custom\LinkedinMessages\Sync */
    $sync = $this->getService(SyncService::class);

    $query = $mAccount->record->prepareReadQuery();
    if ($idAccount > 0) $query = $query->where($mAccount->table . '.id', $idAccount);
    $accounts = $query->get()?->toArray() ?? [];

    if (count($accounts) == 0) {
      return [ 'status' => 'error', 'message' => $this->translate('No LinkedIn account to synchronize.') ];
    }

    $created = 0;
    $messages = [];
    $allOk = true;
    foreach ($accounts as $account) {
      $result = $sync->syncAccount((int) $account['id']);
      $created += $result['created'];
      $allOk = $allOk && $result['ok'];
      $messages[] = $result['info'];
    }

    return [
      'status' => $allOk ? 'success' : 'error',
      'created' => $created,
      'message' => trim(implode(' ', array_filter($messages))),
    ];
  }

}
