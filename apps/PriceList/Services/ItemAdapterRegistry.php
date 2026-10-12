<?php

namespace Hubleto\App\Community\PriceLists\Services;

use Hubleto\App\Community\PriceLists\Loader as PriceListApp;

/**
 * Maps document-item models (items of orders, deals, invoices, receipts, ...) to the field names PriceList needs.
 *
 * Adapters are collected from the extendible PriceListItemAdapters of all enabled apps. An adapter is an array:
 *   productField, quantityField, priceField, discountField (nullable), vatField (nullable),
 *   outputMap (canonical calculator key => column; empty if the item model recalculates its own prices),
 *   parent => [
 *     model (FQN of the parent document model), fkField (column of the item pointing to the parent),
 *     customerField, supplierField, currencyField, dateField, typeField, purchaseValue,
 *     shopField, cashRegisterField  (all optional)
 *   ]
 */
class ItemAdapterRegistry extends \Hubleto\Erp\Core
{
  protected ?array $adapters = null;

  /** All adapters indexed by item model class. */
  public function getAdapters(): array
  {
    if ($this->adapters === null) {
      /** @var PriceListApp|null $app */
      $app = $this->appManager()->getApp('Hubleto\\App\\Community\\PriceLists');
      $this->adapters = $app ? $app->collectExtendibles('PriceListItemAdapters') : [];
    }
    return $this->adapters;
  }

  public function getAdapter(string $itemModelClass): ?array
  {
    $adapters = $this->getAdapters();
    return isset($adapters[$itemModelClass]) && class_exists($itemModelClass) ? $adapters[$itemModelClass] : null;
  }

  public function isRegistered(string $itemModelClass): bool
  {
    return $this->getAdapter($itemModelClass) !== null;
  }

  /**
   * Adapters of item models whose parent document is the given model.
   *
   * @param string $parentModelClass
   *
   * @return array [itemModelClass => adapter]
   *
   */
  public function getAdaptersForParent(string $parentModelClass): array
  {
    $result = [];
    foreach ($this->getAdapters() as $itemModelClass => $adapter) {
      if (($adapter['parent']['model'] ?? '') === $parentModelClass && class_exists($itemModelClass)) {
        $result[$itemModelClass] = $adapter;
      }
    }
    return $result;
  }

  /**
   * Builds the pricing context of a document item: product, quantity, current price and the party/date/currency
   * of its parent document.
   *
   * @param string $itemModelClass
   * @param array $item Record of the document item
   *
   * @return array|null Null if the model has no adapter
   *
   */
  public function describeItem(string $itemModelClass, array $item): ?array
  {
    $adapter = $this->getAdapter($itemModelClass);
    if ($adapter === null) return null;

    $parentConfig = $adapter['parent'] ?? [];
    $idParent = (int) ($item[$parentConfig['fkField'] ?? ''] ?? 0);
    $parent = [];

    if ($idParent > 0 && !empty($parentConfig['model']) && class_exists($parentConfig['model'])) {
      /** @var \Hubleto\Erp\Model $mParent */
      $mParent = $this->getModel($parentConfig['model']);
      $parentRow = $mParent->record->where($mParent->table . '.id', $idParent)->first();
      $parent = $parentRow ? $parentRow->toArray() : [];
    }

    $type = 'sales';
    if (!empty($parentConfig['typeField']) && isset($parent[$parentConfig['typeField']])) {
      if ((int) $parent[$parentConfig['typeField']] === (int) ($parentConfig['purchaseValue'] ?? -1)) $type = 'purchase';
    }

    $date = date('Y-m-d');
    if (!empty($parentConfig['dateField']) && !empty($parent[$parentConfig['dateField']])) {
      $date = substr((string) $parent[$parentConfig['dateField']], 0, 10);
    }

    $idCashRegister = (int) ($parent[$parentConfig['cashRegisterField'] ?? ''] ?? 0);
    $idShop = (int) ($parent[$parentConfig['shopField'] ?? ''] ?? 0);
    if ($idCashRegister > 0 && $idShop <= 0) {
      $cashRegisterClass = 'Hubleto\\App\\Community\\Cashdesk\\Models\\CashRegister';
      if (class_exists($cashRegisterClass)) {
        /** @var \Hubleto\Erp\Model $mCashRegister */
        $mCashRegister = $this->getModel($cashRegisterClass);
        $cashRegister = $mCashRegister->record->where($mCashRegister->table . '.id', $idCashRegister)->first();
        $idShop = (int) ($cashRegister->id_shop ?? 0);
      }
    }

    return [
      'adapter' => $adapter,
      'idProduct' => (int) ($item[$adapter['productField']] ?? 0),
      'qty' => (float) ($item[$adapter['quantityField']] ?? 1),
      'unitPrice' => (float) ($item[$adapter['priceField']] ?? 0),
      'discount' => !empty($adapter['discountField']) ? (float) ($item[$adapter['discountField']] ?? 0) : 0.0,
      'vat' => !empty($adapter['vatField']) ? (float) ($item[$adapter['vatField']] ?? 0) : 0.0,
      'parentModel' => (string) ($parentConfig['model'] ?? ''),
      'idParent' => $idParent,
      'type' => $type,
      'date' => $date,
      'idCustomer' => (int) ($parent[$parentConfig['customerField'] ?? ''] ?? 0),
      'idSupplier' => (int) ($parent[$parentConfig['supplierField'] ?? ''] ?? 0),
      'idCurrency' => (int) ($parent[$parentConfig['currencyField'] ?? ''] ?? 0),
      'idShop' => $idShop,
      'idCashRegister' => $idCashRegister,
    ];
  }

  /** Builds a PriceResolver query array from the result of describeItem(). */
  public function toResolverQuery(array $context): array
  {
    return [
      'idProduct' => $context['idProduct'],
      'qty' => $context['qty'],
      'date' => $context['date'],
      'type' => $context['type'],
      'idCustomer' => $context['idCustomer'],
      'idSupplier' => $context['idSupplier'],
      'idCurrency' => $context['idCurrency'],
      'idShop' => $context['idShop'],
      'idCashRegister' => $context['idCashRegister'],
    ];
  }
}
