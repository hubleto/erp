<?php

namespace Hubleto\App\Community\PriceList\Extendibles;

use Hubleto\App\Community\Cashdesk\Models\Receipt;
use Hubleto\App\Community\Cashdesk\Models\ReceiptItem;
use Hubleto\App\Community\Deals\Models\Deal;
use Hubleto\App\Community\Deals\Models\Item as DealItem;
use Hubleto\App\Community\Invoices\Models\Invoice;
use Hubleto\App\Community\Invoices\Models\Item as InvoiceItem;
use Hubleto\App\Community\Orders\Models\Item as OrderItem;
use Hubleto\App\Community\Orders\Models\Order;

/**
 * Declares document-item models that PriceList can price. Other apps can provide their own
 * Extendibles\PriceListItemAdapters class with the same shape (see community-apps-patches.md).
 *
 * Adapter keys:
 *   productField, quantityField, priceField, discountField, vatField,
 *   outputMap (canonical calculator key => column; leave empty when the item model recalculates its own prices),
 *   parent => model, fkField, customerField, supplierField, currencyField, dateField,
 *             typeField, purchaseValue, shopField, cashRegisterField
 */
class PriceListItemAdapters extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      OrderItem::class => [
        'productField' => 'id_product', 'quantityField' => 'amount',
        'priceField' => 'unit_price', 'discountField' => 'discount', 'vatField' => 'vat',
        'outputMap' => [],
        'parent' => [
          'model' => Order::class, 'fkField' => 'id_order',
          'customerField' => 'id_customer', 'supplierField' => 'id_supplier', 'currencyField' => 'id_currency',
          'dateField' => 'date_order', 'typeField' => 'purchase_sales', 'purchaseValue' => Order::PURCHASE_ORDER,
        ],
      ],
      DealItem::class => [
        'productField' => 'id_product', 'quantityField' => 'amount',
        'priceField' => 'unit_price', 'discountField' => 'discount', 'vatField' => 'vat',
        'outputMap' => [],
        'parent' => [
          'model' => Deal::class, 'fkField' => 'id_deal',
          'customerField' => 'id_customer', 'currencyField' => 'id_currency',
        ],
      ],
      InvoiceItem::class => [
        'productField' => 'id_product', 'quantityField' => 'amount',
        'priceField' => 'unit_price', 'discountField' => 'discount', 'vatField' => 'vat',
        'outputMap' => [],
        'parent' => [
          'model' => Invoice::class, 'fkField' => 'id_invoice',
          'customerField' => 'id_customer', 'currencyField' => 'id_currency', 'dateField' => 'date_issue',
        ],
      ],
      ReceiptItem::class => [
        'productField' => 'id_product', 'quantityField' => 'quantity',
        'priceField' => 'unit_price_excl_vat', 'discountField' => null, 'vatField' => 'vat_percent',
        'outputMap' => [
          'unit_price_excl_vat' => 'unit_price_excl_vat', 'unit_vat' => 'unit_vat', 'unit_price_incl_vat' => 'unit_price_incl_vat',
          'price_excl_vat' => 'total_price_excl_vat', 'price_vat' => 'total_vat', 'price_incl_vat' => 'total_price_incl_vat',
        ],
        'parent' => [
          'model' => Receipt::class, 'fkField' => 'id_receipt', 'cashRegisterField' => 'id_cash_register', 'dateField' => 'created',
        ],
      ],
    ];
  }
}
