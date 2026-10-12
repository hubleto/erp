<?php

namespace Hubleto\App\Community\PriceList\Controllers\Api;

use Hubleto\App\Community\PriceList\Models\History;
use Hubleto\App\Community\PriceList\Models\Item;
use Hubleto\App\Community\PriceList\Services\HistoryWriter;
use Hubleto\App\Community\PriceList\Services\PriceCalculator;
use Hubleto\App\Community\PriceList\Services\Settings;

/**
 * Changes fixed prices of a price list by a percentage or an amount.
 * Parameters: idPriceList, mode ('percent' or 'amount'), value (negative lowers prices), idCategory, idProduct,
 * preview (true = only returns what would change).
 */
class BulkAdjust extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $router = $this->router();
    $idPriceList = $router->urlParamAsInteger('idPriceList');
    $mode = $router->urlParamAsString('mode', 'percent');
    $value = $router->urlParamAsFloat('value');
    $preview = $router->urlParamAsBool('preview');

    if ($idPriceList <= 0 || $value == 0.0) {
      return ['status' => 'error', 'message' => $this->translate('Select a price list and a non-zero value.')];
    }

    /** @var PriceCalculator $calc */
    $calc = $this->getService(PriceCalculator::class);
    /** @var Settings $settings */
    $settings = $this->getService(Settings::class);
    /** @var HistoryWriter $historyWriter */
    $historyWriter = $this->getService(HistoryWriter::class);
    /** @var Item $mItem */
    $mItem = $this->getModel(Item::class);
    $t = $mItem->table;

    $query = $mItem->record
      ->where($t . '.id_price_list', $idPriceList)
      ->where($t . '.method', Item::METHOD_FIXED)
    ;
    if ($router->urlParamAsInteger('idCategory') > 0) $query = $query->where($t . '.id_category', $router->urlParamAsInteger('idCategory'));
    if ($router->urlParamAsInteger('idProduct') > 0) $query = $query->where($t . '.id_product', $router->urlParamAsInteger('idProduct'));

    $changes = [];
    foreach ($query->get() as $row) {
      $old = (float) $row->value;
      $new = $mode === 'amount' ? $calc->addAmount($old, $value) : $calc->applyPercentOn($old, $value);
      $new = $calc->round($new, ['decimals' => $settings->getInt('calc.decimals.unit')]);
      if ($new < 0) continue;
      $changes[] = ['id' => (int) $row->id, 'old' => $old, 'new' => $new];
    }

    if (!$preview) {
      foreach ($changes as $change) {
        $row = $mItem->record->where($t . '.id', $change['id'])->first();
        $mItem->record->recordUpdate(array_merge($row->getAttributes(), ['value' => $change['new']]));
      }
      $historyWriter->write(
        $idPriceList,
        History::EVENT_BULK_ADJUSTED,
        $this->translate('Prices adjusted') . ': ' . $value . ($mode === 'amount' ? '' : ' %') . ' (' . count($changes) . ')',
        [],
        'bulk'
      );
    }

    return ['status' => 'success', 'changed' => count($changes), 'preview' => $preview ? $changes : []];
  }
}
