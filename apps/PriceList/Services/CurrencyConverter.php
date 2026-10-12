<?php

namespace Hubleto\App\Community\PriceList\Services;

use Hubleto\App\Community\Settings\Models\Setting;
use Hubleto\App\Custom\Settings\Models\CurrencyRate;

/**
 * Converts amounts between currencies using the rates stored in CurrencyRate (rate to the default currency).
 */
class CurrencyConverter extends \Hubleto\Erp\Core
{
  /** Id of the default currency (0 if not set). */
  public function getDefaultCurrencyId(): int
  {
    /** @var Setting $mSettings */
    $mSettings = $this->getModel(Setting::class);

    return (int) $mSettings->record
      ->where('key', 'Apps\\Community\\Settings\\Currency\\DefaultCurrency')
      ->first()
      ?->value
    ;
  }

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
    if ($idCurrency <= 0 || $idCurrency === $this->getDefaultCurrencyId()) return 1.0;

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
