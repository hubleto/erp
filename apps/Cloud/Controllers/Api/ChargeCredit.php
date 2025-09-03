<?php

namespace Hubleto\App\Community\Cloud\Controllers\Api;

use Hubleto\App\Community\Cloud\PremiumAccount;

class ChargeCredit extends \Hubleto\Erp\Controllers\ApiController
{
  public bool $requiresAuthenticatedUser = false;

  public function renderJson(): ?array
  {
    $premiumAccount = $this->getService(PremiumAccount::class);

    $mDiscount = $this->getModel(\Hubleto\App\Community\Cloud\Models\Discount::class);
    $mPayment = $this->getModel(\Hubleto\App\Community\Cloud\Models\Payment::class);

    $discountThisMonth = $mDiscount->record
      ->where('month', date('m'))
      ->where('year', date('Y'))
      ->first()
      ?->toArray()
    ;

    $paymentThisMonth = $mPayment->record
      ->whereMonth('datetime_charged', date('m'))
      ->whereYear('datetime_charged', date('Y'))
      ->where('full_amount', '<', 0)
      ->first()
      ?->toArray()
    ;

    $premiumInfo = $premiumAccount->getPremiumInfo();

    $amountThisMonth = 0;
    if (is_array($paymentThisMonth)) {
      $amountThisMonth = (float) ($paymentThisMonth['full_amount'] ?? 0);
    }

    $discountPercent = $discountThisMonth['discount_percent'] ?? 0;

    $fullAmount = $premiumAccount->getPrice(
      $premiumInfo['activeUsers'],
      $premiumInfo['paidApps'],
      0
    );

    $discountedAmount = $premiumAccount->getPrice(
      $premiumInfo['activeUsers'],
      $premiumInfo['paidApps'],
      $discountPercent
    );

    if ($fullAmount > $amountThisMonth) {
      $paymentData = [
        'datetime_charged' => date('Y-m-d H:i:s'),
        'full_amount' => -$fullAmount,
        'discounted_amount' => -$discountedAmount,
        'discount_percent' => $discountPercent,
      ];

      if ($paymentThisMonth === null) {
        $mPayment->record->recordCreate($paymentData);
      } else {
        $paymentData['id'] = $paymentThisMonth['id'];
        $mPayment->record->recordUpdate($paymentData);
      }
    }

    $premiumAccount->recalculateCredit();

    $currentCredit = $premiumAccount->getCurrentCredit();

    if ($currentCredit <= 0) {
      // ak je nastavena platba kartou, stiahnut prislusnu sumu a dorovnat kredit na 0
    }

    return [
      'success' => true,
    ];

  }

}
