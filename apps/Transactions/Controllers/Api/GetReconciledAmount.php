<?php

namespace Hubleto\App\Community\Transactions\Controllers\Api;

use Hubleto\App\Community\Transactions\Models\Reconciliation;
use Hubleto\App\Community\Transactions\Models\Transaction;
use Hubleto\Erp\Controllers\ApiController;

class GetReconciledAmount extends ApiController
{


  public function renderJson(): ?array
  {

    $mTransaction = $this->getModel(Transaction::class);

    if ($this->router()->urlParamAsInteger('transactionId') > 0) {
      $transaction = $mTransaction->record->find($this->router()->urlParamAsInteger('transactionId'));

      $mReconciliation = $this->getModel(Reconciliation::class);
      $result = $mReconciliation->record
        ->where('id_transaction', $transaction->id)
        ->join('journal_entry_line', 'journal_entry_line.id', '=', 'transactions_reconciliation.id_journal_entry_line')
        ->sum('journal_entry_line.amount');

      return ['reconciledAmount' => $result];
    }

    return ['status' => 'bad request'];
  }

}
