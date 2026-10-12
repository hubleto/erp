<?php

namespace Hubleto\App\Community\PriceLists\Controllers\Api;

use Hubleto\App\Community\PriceLists\Services\ItemAdapterRegistry;
use Hubleto\App\Community\PriceLists\Services\PriceCalculator;
use Hubleto\App\Community\PriceLists\Services\PriceResolver;
use Hubleto\App\Community\PriceLists\Services\Settings;

/**
 * Prices the items of a document (order, deal, invoice, receipt, ...) with the price lists.
 * Parameters: model (parent document model, '/' or '\' separated), idRecord, mode ('when_zero' or 'always').
 */
class ApplyToDocument extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    /** @var ItemAdapterRegistry $registry */
    $registry = $this->getService(ItemAdapterRegistry::class);
    /** @var PriceResolver $resolver */
    $resolver = $this->getService(PriceResolver::class);
    /** @var PriceCalculator $calc */
    $calc = $this->getService(PriceCalculator::class);
    /** @var Settings $settings */
    $settings = $this->getService(Settings::class);

    $parentModel = str_replace('/', '\\', $this->router()->urlParamAsString('model'));
    $idRecord = $this->router()->urlParamAsInteger('idRecord');
    $mode = $this->router()->urlParamAsString('mode', $settings->getString('applyToDocumentMode'));

    $adapters = $registry->getAdaptersForParent($parentModel);
    if (empty($adapters) || $idRecord <= 0) {
      return ['status' => 'error', 'message' => $this->translate('This document cannot be priced with price lists.')];
    }

    $updated = 0;
    $skipped = [];

    foreach ($adapters as $itemModelClass => $adapter) {
      /** @var \Hubleto\Erp\Model $mItem */
      $mItem = $this->getModel($itemModelClass);
      $items = $mItem->record
        ->where($mItem->table . '.' . $adapter['parent']['fkField'], $idRecord)
        ->get()
      ;

      foreach ($items as $itemRow) {
        $item = $itemRow->getAttributes();
        $context = $registry->describeItem($itemModelClass, $item);
        $idItem = (int) $item['id'];

        if ($context === null || $context['idProduct'] <= 0) { $skipped[] = ['id' => $idItem, 'reason' => 'no product']; continue; }
        if ($mode === 'when_zero' && $context['unitPrice'] > 0) { $skipped[] = ['id' => $idItem, 'reason' => 'price set']; continue; }

        $result = $resolver->resolve($registry->toResolverQuery($context));
        if ($result['source'] === 'NONE') { $skipped[] = ['id' => $idItem, 'reason' => 'no price']; continue; }

        $changes = [$adapter['priceField'] => $result['unitPriceExclVat']];
        if (!empty($adapter['discountField'])) $changes[$adapter['discountField']] = $result['discountPercent'];
        if (!empty($adapter['vatField']) && $context['vat'] == 0.0) $changes[$adapter['vatField']] = $result['vatPercent'];

        // models that do not recalculate their own prices get the calculated values through the output map
        if (!empty($adapter['outputMap'])) {
          $calculated = $calc->calculateItem([
            'unit_price' => $changes[$adapter['priceField']],
            'amount' => $context['qty'],
            'discount' => (float) ($changes[$adapter['discountField'] ?? ''] ?? $context['discount']),
            'vat' => (float) ($changes[$adapter['vatField'] ?? ''] ?? $context['vat']),
          ], ['vat_basis' => 'unit']);
          foreach ($adapter['outputMap'] as $canonical => $column) {
            $changes[$column] = $calculated[$canonical];
          }
        }

        $mItem->record->recordUpdate(array_merge($item, $changes));
        $updated++;
      }
    }

    return ['status' => 'success', 'updated' => $updated, 'skipped' => $skipped];
  }
}
