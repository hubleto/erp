<?php

namespace Hubleto\App\Community\PriceLists\EventListeners;

use Hubleto\App\Community\PriceLists\Models\History;
use Hubleto\App\Community\PriceLists\Models\Resolution;
use Hubleto\App\Community\PriceLists\Services\HistoryWriter;
use Hubleto\App\Community\PriceLists\Services\ItemAdapterRegistry;
use Hubleto\App\Community\PriceLists\Services\PriceCalculator;
use Hubleto\App\Community\PriceLists\Services\PriceResolver;
use Hubleto\App\Community\PriceLists\Services\Settings;
use Hubleto\Framework\Exceptions\RecordSaveException;
use Hubleto\Framework\Interfaces\ModelInterface;

/**
 * Watches items of documents (orders, deals, invoices, receipts, ...) that have an adapter in ItemAdapterRegistry.
 *
 * Before saving: optionally refuses an item whose price is below the minimum of the matched price list
 *   (setting blockBelowFloor).
 * After saving: logs the resolved price (Resolution), marks deviations, and - only if the setting applyMode
 *   is `when_zero` or `always` - writes the resolved price back to the item.
 *
 * A listener cannot change the record that is being saved, which is why write-back happens after saving.
 * Explicit pricing of a whole document is the "Apply price list" button (Controllers/Api/ApplyToDocument).
 */
class ApplyAndAuditItemPrice extends \Hubleto\Framework\EventListener implements \Hubleto\Framework\Interfaces\EventListenerInterface
{
  /** @var array<string, bool> Re-entrancy guard: write-back calls recordUpdate(), which fires these events again. */
  protected static array $writing = [];

  public function onModelBeforeCreate(ModelInterface $model, array $record): void
  {
    if (!$this->isPricedItemModel($model)) return;
    $this->refuseBelowFloor($model, $record);
  }

  public function onModelBeforeUpdate(ModelInterface $model, array $record): void
  {
    if (!$this->isPricedItemModel($model)) return;
    $this->refuseBelowFloor($model, $record);
  }

  public function onModelAfterCreate(ModelInterface $model, array $savedRecord): void
  {
    if (!$this->isPricedItemModel($model)) return;
    $this->auditAndApply($model, $savedRecord);
  }

  public function onModelAfterUpdate(ModelInterface $model, array $originalRecord, array $savedRecord): void
  {
    if (!$this->isPricedItemModel($model)) return;
    $this->auditAndApply($model, $savedRecord);
  }

  protected function isPricedItemModel(ModelInterface $model): bool
  {
    /** @var ItemAdapterRegistry $registry */
    $registry = $this->getService(ItemAdapterRegistry::class);
    return $registry->isRegistered(get_class($model));
  }

  /** Throws if the item price is below the floor of the matched price (only when blockBelowFloor is on). */
  protected function refuseBelowFloor(ModelInterface $model, array $record): void
  {
    /** @var Settings $settings */
    $settings = $this->getService(Settings::class);
    if (!$settings->getBool('blockBelowFloor')) return;

    /** @var ItemAdapterRegistry $registry */
    $registry = $this->getService(ItemAdapterRegistry::class);
    $context = $registry->describeItem(get_class($model), $record);
    if ($context === null || $context['idProduct'] <= 0) return;

    /** @var PriceResolver $resolver */
    $resolver = $this->getService(PriceResolver::class);
    $result = $resolver->resolve($registry->toResolverQuery($context));
    if ($result['floorPrice'] === null) return;

    /** @var PriceCalculator $calc */
    $calc = $this->getService(PriceCalculator::class);
    $netUnitPrice = $calc->calculateDiscountedPrice($context['unitPrice'], $context['discount']);

    if ($netUnitPrice < (float) $result['floorPrice']) {
      throw new RecordSaveException(
        $this->translate('The price is below the minimum allowed by the price list.'),
        [$context['adapter']['priceField']]
      );
    }
  }

  protected function auditAndApply(ModelInterface $model, array $savedRecord): void
  {
    $key = get_class($model) . ':' . ($savedRecord['id'] ?? 0);
    if (isset(self::$writing[$key])) return;

    try {
      /** @var ItemAdapterRegistry $registry */
      $registry = $this->getService(ItemAdapterRegistry::class);
      $context = $registry->describeItem(get_class($model), $savedRecord);
      if ($context === null || $context['idProduct'] <= 0) return;

      /** @var PriceResolver $resolver */
      $resolver = $this->getService(PriceResolver::class);
      /** @var PriceCalculator $calc */
      $calc = $this->getService(PriceCalculator::class);
      /** @var Settings $settings */
      $settings = $this->getService(Settings::class);

      $result = $resolver->resolve($registry->toResolverQuery($context));
      if ($result['source'] === 'NONE') return;

      $deviationPercent = abs($calc->calculateDiscountPercent((float) $result['unitPriceExclVat'], $context['unitPrice']));
      $deviates = $result['unitPriceExclVat'] > 0 && $deviationPercent > $settings->getFloat('deviationTolerancePercent');

      /** @var Resolution $mResolution */
      $mResolution = $this->getModel(Resolution::class);
      $mResolution->record->where($mResolution->table . '.model', get_class($model))->where($mResolution->table . '.record_id', (int) $savedRecord['id'])->delete();
      $mResolution->record->recordCreate([
        'model' => get_class($model),
        'record_id' => (int) $savedRecord['id'],
        'parent_model' => $context['parentModel'],
        'parent_record_id' => $context['idParent'],
        'id_price_list' => $result['idPriceList'] > 0 ? $result['idPriceList'] : null,
        'id_item' => $result['idItem'] > 0 ? $result['idItem'] : null,
        'id_product' => $context['idProduct'],
        'qty' => $context['qty'],
        'base_price' => $result['basePrice'],
        'unit_price' => $result['unitPriceExclVat'],
        'discount_percent' => $result['discountPercent'],
        'item_unit_price' => $context['unitPrice'],
        'deviates' => $deviates,
        'trace' => json_encode($result['trace']),
        'date_time' => date('Y-m-d H:i:s'),
      ]);

      if ($result['marginFloorHit'] && $result['idPriceList'] > 0) {
        /** @var HistoryWriter $historyWriter */
        $historyWriter = $this->getService(HistoryWriter::class);
        $historyWriter->write($result['idPriceList'], History::EVENT_MARGIN_FLOOR_HIT, $this->translate('Minimum margin applied to a document item'), ['id_product' => $context['idProduct'], 'new_value' => $result['unitPrice']], 'listener');
      }

      $mode = $settings->getString('applyMode');
      if ($mode === 'off') return;
      if ($mode === 'when_zero' && $context['unitPrice'] > 0) return;

      $adapter = $context['adapter'];
      $changes = [$adapter['priceField'] => $result['unitPriceExclVat']];
      if (!empty($adapter['discountField']) && $context['discount'] == 0.0) $changes[$adapter['discountField']] = $result['discountPercent'];
      if (!empty($adapter['vatField']) && $context['vat'] == 0.0) $changes[$adapter['vatField']] = $result['vatPercent'];

      self::$writing[$key] = true;
      try {
        $model->record->recordUpdate(array_merge($savedRecord, $changes));
      } finally {
        unset(self::$writing[$key]);
      }
    } catch (\Throwable $e) {
      // pricing audit must never block saving of a document item
    }
  }
}
