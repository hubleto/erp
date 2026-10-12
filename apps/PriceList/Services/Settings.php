<?php

namespace Hubleto\App\Community\PriceList\Services;

use Hubleto\App\Community\PriceList\Loader as PriceListApp;

/**
 * Typed access to the settings of the PriceList app.
 * Settings are stored in the config under apps/Hubleto\App\Community\PriceList/<key>.
 */
class Settings extends \Hubleto\Erp\Core
{
  public const CONFIG_PREFIX = 'apps/Hubleto\\App\\Custom\\PriceList/';

  /** Default values of all settings. */
  public const DEFAULTS = [
    'numberingPattern' => 'PL{YY}-{#}',
    'calc.decimals.unit' => 4,
    'calc.decimals.item' => 2,
    'calc.rounding' => 'none',
    'calc.vatMode' => 'per_item',
    'calc.cashRounding' => 0,
    'vat.requireRate' => 0,
    'resolution.fallbackToProductPrice' => 1,
    'applyMode' => 'off',
    'applyToDocumentMode' => 'when_zero',
    'deviationTolerancePercent' => 0.5,
    'notifyOnDeviation' => 0,
    'blockBelowFloor' => 0,
    'syncSupplierPrices' => 0,
    'expiryWarningDays' => 14,
    'resolutionRetentionDays' => 365,
    'maxBatchItems' => 500,
  ];

  public function getString(string $key): string
  {
    $default = self::DEFAULTS[$key] ?? '';
    return $this->config()->forApp(PriceListApp::class)->getAsString($key, (string) $default);
  }

  public function getInt(string $key): int
  {
    $default = self::DEFAULTS[$key] ?? 0;
    return $this->config()->forApp(PriceListApp::class)->getAsInteger($key, (int) $default);
  }

  public function getFloat(string $key): float
  {
    $default = self::DEFAULTS[$key] ?? 0;
    return $this->config()->forApp(PriceListApp::class)->getAsFloat($key, (float) $default);
  }

  public function getBool(string $key): bool
  {
    $default = self::DEFAULTS[$key] ?? 0;
    return $this->config()->forApp(PriceListApp::class)->getAsBool($key, (bool) $default);
  }

  /**
   * Saves a setting. The full config path is used because ConfigManager::save() ignores the prefix set by forApp().
   */
  public function save(string $key, string|int|float|bool $value): void
  {
    $this->config()->save(self::CONFIG_PREFIX . $key, is_bool($value) ? ($value ? '1' : '0') : (string) $value);
  }

  /**
   * Options for PriceCalculator::calculateItem() and calculateTotals() taken from settings.
   * Falls back to hard-coded defaults when settings are not available.
   */
  public function getCalculatorOptions(): array
  {
    $options = [
      'rounding' => 'none',
      'vat_mode' => 'per_item',
      'decimals_unit' => 4,
      'decimals_item' => 2,
      'cash_rounding' => 0.0,
    ];

    try {
      $options['rounding'] = $this->getString('calc.rounding');
      $options['vat_mode'] = $this->getString('calc.vatMode');
      $options['decimals_unit'] = $this->getInt('calc.decimals.unit');
      $options['decimals_item'] = $this->getInt('calc.decimals.item');
      $options['cash_rounding'] = $this->getFloat('calc.cashRounding');
    } catch (\Throwable $e) {
      // settings not available (e.g., in unit tests): keep defaults
    }

    return $options;
  }
}
