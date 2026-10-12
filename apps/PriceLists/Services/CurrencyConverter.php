<?php

namespace Hubleto\App\Community\PriceLists\Services;

use Hubleto\App\Community\Settings\Services\Currency as CurrencyService;
use Hubleto\App\Community\Settings\Models\CurrencyRate;

/**
 * Converts amounts between currencies using the rates stored in CurrencyRate (rate to the default currency).
 */
class CurrencyConverter extends \Hubleto\Erp\Core
{
  /**
   * Units of the default currency for 1 unit of the given currency, valid on the date. Null if no rate is known.
   *
   * @param int $idCurrency
   * @param string $date
   *
   * @return float|null
   *
   */
  public function getRateToDefault(int $idCurrency, string $date): ?float
  {
    /** @var CurrencyService */
    $currencyService = $this->getService(CurrencyService::class);

    if ($idCurrency <= 0 || $idCurrency === $currencyService->getDefaultCurrencyId()) return 1.0;

    /** @var CurrencyRate $mRate */
    $mRate = $this->getModel(CurrencyRate::class);
    $rate = $mRate->record
      ->where($mRate->table . '.id_currency', $idCurrency)
      ->where($mRate->table . '.valid_from', '<=', $date)
      ->orderBy($mRate->table . '.valid_from', 'desc')
      ->first()
    ;

    return $rate ? (float) $rate->rate_to_default : null;
  }

  /**
   * Converts an amount. Returns null when a rate is missing.
   *
   * @param float $amount
   * @param int $idFrom
   * @param int $idTo
   * @param string $date
   *
   * @return float|null
   *
   */
  public function convert(float $amount, int $idFrom, int $idTo, string $date): ?float
  {
    if ($idFrom <= 0 || $idTo <= 0 || $idFrom === $idTo) return $amount;

    $rateFrom = $this->getRateToDefault($idFrom, $date);
    $rateTo = $this->getRateToDefault($idTo, $date);
    if ($rateFrom === null || $rateTo === null || $rateTo <= 0) return null;

    /** @var PriceCalculator $calc */
    $calc = $this->getService(PriceCalculator::class);

    return $calc->convertCurrency($calc->convertCurrency($amount, $rateFrom), 1 / $rateTo);
  }
}
