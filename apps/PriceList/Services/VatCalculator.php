<?php

namespace Hubleto\App\Community\PriceList\Services;

use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\Community\Products\Models\Product;
use Hubleto\App\Community\PriceList\Models\Item;
use Hubleto\App\Community\Settings\Models\VatRate;

/**
 * VAT arithmetic and VAT rate resolution.
 */
class VatCalculator extends \Hubleto\Erp\Core
{
  public function vatFromBase(float $base, float $percent): float
  {
    return $base * $percent / 100;
  }

  public function baseFromGross(float $gross, float $percent): float
  {
    return $gross / (1 + $percent / 100);
  }

  public function vatFromGross(float $gross, float $percent): float
  {
    return $gross - $this->baseFromGross($gross, $percent);
  }

  public function grossFromBase(float $base, float $percent): float
  {
    return $base * (1 + $percent / 100);
  }

  /**
   * Groups items by VAT rate.
   *
   * @param array $items Items with keys vat and price_excl_vat (and optionally price_vat)
   *
   * @return array List of ['rate', 'base', 'vat', 'gross']
   *
   */
  public function summarize(array $items): array
  {
    $groups = [];
    foreach ($items as $item) {
      if (!empty($item['_toBeDeleted_'])) continue;
      $rate = (float) ($item['vat'] ?? 0);
      $key = (string) $rate;
      if (!isset($groups[$key])) $groups[$key] = ['rate' => $rate, 'base' => 0.0, 'vat' => 0.0, 'gross' => 0.0];
      $base = (float) ($item['price_excl_vat'] ?? 0);
      $vat = isset($item['price_vat']) ? (float) $item['price_vat'] : $this->vatFromBase($base, $rate);
      $groups[$key]['base'] += $base;
      $groups[$key]['vat'] += $vat;
      $groups[$key]['gross'] += $base + $vat;
    }
    return array_values($groups);
  }

  /**
   * Resolves the VAT rate.
   *
   * Query keys: vat (explicit), idPrice (PriceList item), idProduct, idCountry, idCustomer, date, type (VatRate type).
   * Precedence: explicit vat, VAT rate of the matched price, Product.vat, dated VatRate for the country and type,
   * default VatRate, 0 % (or an exception when the setting vat.requireRate is on).
   *
   * @param array $query
   *
   * @return array ['percent', 'idVatRate', 'type', 'note', 'source']
   *
   */
  public function resolveRate(array $query): array
  {
    $date = (string) ($query['date'] ?? date('Y-m-d'));

    if (isset($query['vat']) && $query['vat'] !== '' && $query['vat'] !== null) {
      return ['percent' => (float) $query['vat'], 'idVatRate' => 0, 'type' => VatRate::TYPE_STANDARD, 'note' => '', 'source' => 'explicit'];
    }

    /** @var VatRate $mVatRate */
    $mVatRate = $this->getModel(VatRate::class);

    $idPrice = (int) ($query['idPrice'] ?? 0);
    if ($idPrice > 0) {
      /** @var Item $mItem */
      $mItem = $this->getModel(Item::class);
      $price = $mItem->record->where($mItem->table . '.id', $idPrice)->first();
      if ($price && (int) $price->id_vat_rate > 0) {
        $rate = $mVatRate->record->where($mVatRate->table . '.id', (int) $price->id_vat_rate)->first();
        if ($rate) return $this->formatRate($rate, 'price');
      }
    }

    $idProduct = (int) ($query['idProduct'] ?? 0);
    if ($idProduct > 0) {
      /** @var Product $mProduct */
      $mProduct = $this->getModel(Product::class);
      $product = $mProduct->record->where($mProduct->table . '.id', $idProduct)->first();
      if ($product && $product->vat !== null && $product->vat !== '') {
        return ['percent' => (float) $product->vat, 'idVatRate' => 0, 'type' => VatRate::TYPE_STANDARD, 'note' => '', 'source' => 'product'];
      }
    }

    $idCountry = (int) ($query['idCountry'] ?? 0);
    $idCustomer = (int) ($query['idCustomer'] ?? 0);
    if ($idCountry <= 0 && $idCustomer > 0) {
      /** @var Customer $mCustomer */
      $mCustomer = $this->getModel(Customer::class);
      $customer = $mCustomer->record->where($mCustomer->table . '.id', $idCustomer)->first();
      $idCountry = (int) ($customer->id_country ?? 0);
    }

    $type = (int) ($query['type'] ?? 0);
    if ($type > 0) {
      $rate = $this->findRate($mVatRate, $idCountry, $date, $type, false);
      if ($rate) return $this->formatRate($rate, 'dated');
    }

    $rate = $this->findRate($mVatRate, $idCountry, $date, 0, true);
    if ($rate) return $this->formatRate($rate, 'default');

    /** @var Settings $settings */
    $settings = $this->getService(Settings::class);
    if ($settings->getBool('vat.requireRate')) {
      throw new \Exception($this->translate('VAT rate could not be determined.'));
    }

    return ['percent' => 0.0, 'idVatRate' => 0, 'type' => VatRate::TYPE_ZERO, 'note' => '', 'source' => 'fallback'];
  }

  protected function findRate(VatRate $mVatRate, int $idCountry, string $date, int $type, bool $defaultOnly): mixed
  {
    $table = $mVatRate->table;

    $query = $mVatRate->record
      ->where(function ($q) use ($table, $date) {
        $q->whereNull($table . '.valid_from')->orWhere($table . '.valid_from', '<=', $date);
      })
      ->where(function ($q) use ($table, $date) {
        $q->whereNull($table . '.valid_to')->orWhere($table . '.valid_to', '>=', $date);
      })
      ->where(function ($q) use ($table, $idCountry) {
        if ($idCountry > 0) $q->where($table . '.id_country', $idCountry)->orWhereNull($table . '.id_country');
        else $q->whereNull($table . '.id_country');
      })
    ;

    if ($type > 0) $query = $query->where($table . '.type', $type);
    if ($defaultOnly) $query = $query->where($table . '.is_default', 1);

    return $query->orderByRaw($table . '.id_country is null asc')->first();
  }

  protected function formatRate(mixed $rate, string $source): array
  {
    $type = (int) $rate->type;
    $notes = [
      VatRate::TYPE_EXEMPT => $this->translate('VAT exempt'),
      VatRate::TYPE_REVERSE_CHARGE => $this->translate('Reverse charge'),
    ];
    $zeroTypes = [VatRate::TYPE_ZERO, VatRate::TYPE_EXEMPT, VatRate::TYPE_REVERSE_CHARGE];

    return [
      'percent' => in_array($type, $zeroTypes) ? 0.0 : (float) $rate->percent,
      'idVatRate' => (int) $rate->id,
      'type' => $type,
      'note' => $notes[$type] ?? '',
      'source' => $source,
    ];
  }
}
