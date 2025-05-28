<?php

namespace HubletoApp\Community\Cloud\Controllers\Api;

class ChargeCredit extends \HubletoMain\Core\Controllers\Controller {

  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    $mPayment = new \HubletoApp\Community\Cloud\Models\Payment($this->main);

    $paymentThisMonth = $mPayment->record
      ->whereMonth('datetime_charged', date('m'))
      ->whereYear('datetime_charged', date('Y'))
      ->where('amount', '<', 0)
      ->first()
      ?->toArray()
    ;

    $premiumInfo = $this->hubletoApp->getPremiumInfo();

    $amountThisMonth = 0;
    if (is_array($paymentThisMonth)) {
      $amountThisMonth = (float) ($paymentThisMonth['amount'] ?? 0);
    }

    $price = $this->hubletoApp->getPrice($premiumInfo['activeUsers'], $premiumInfo['paidApps']);

    if ($price > $amountThisMonth) {
      $paymentNotes = $premiumInfo['activeUsers'] . ' active users, ' . $premiumInfo['paidApps'] . ' paid apps';
      if ($paymentThisMonth === null) {
        $mPayment->record->recordCreate(['datetime_charged' => date('Y-m-d H:i:s'), 'amount' => -$price, 'notes' => $paymentNotes]);
      } else {
        $mPayment->record->recordUpdate(['id' => $paymentThisMonth['id'], 'amount' => -$price, 'notes' => $paymentNotes]);
      }
    }

    $this->hubletoApp->recalculateCredit();

    return [
      'premiumInfo' => $premiumInfo,
      'paymentThisMonth' => $paymentThisMonth,
      'amountThisMonth' => $amountThisMonth,
      'price' => $price,
    ];

  }

}