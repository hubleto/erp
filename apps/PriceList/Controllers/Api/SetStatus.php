<?php

namespace Hubleto\App\Community\PriceLists\Controllers\Api;

use Hubleto\App\Community\PriceLists\Models\Item;
use Hubleto\App\Community\PriceLists\Models\PriceList;

/**
 * Changes status of a price list (draft, active, archived).
 */
class SetStatus extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idPriceList = $this->router()->urlParamAsInteger('idPriceList');
    $status = $this->router()->urlParamAsInteger('status');

    if (!in_array($status, [PriceList::STATUS_DRAFT, PriceList::STATUS_ACTIVE, PriceList::STATUS_ARCHIVED])) {
      return ['status' => 'error', 'message' => $this->translate('Unknown status.')];
    }

    /** @var PriceList $mPriceList */
    $mPriceList = $this->getModel(PriceList::class);
    $priceList = $mPriceList->record->where($mPriceList->table . '.id', $idPriceList)->first();
    if (!$priceList) return ['status' => 'error', 'message' => $this->translate('Price list was not found.')];

    if ($status === PriceList::STATUS_ACTIVE && (int) $priceList->basis === PriceList::BASIS_OWN_ITEMS) {
      /** @var Item $mItem */
      $mItem = $this->getModel(Item::class);
      if ($mItem->record->where($mItem->table . '.id_price_list', $idPriceList)->count() == 0) {
        return ['status' => 'error', 'message' => $this->translate('A price list without any price cannot be activated.')];
      }
    }

    try {
      $mPriceList->record->recordUpdate(array_merge($priceList->getAttributes(), ['status' => $status]));
    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }

    return ['status' => 'success'];
  }
}
