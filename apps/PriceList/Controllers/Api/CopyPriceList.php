<?php

namespace Hubleto\App\Community\PriceList\Controllers\Api;

use Hubleto\App\Community\PriceList\Models\Assignment;
use Hubleto\App\Community\PriceList\Models\History;
use Hubleto\App\Community\PriceList\Models\Item;
use Hubleto\App\Community\PriceList\Models\PriceList;
use Hubleto\App\Community\PriceList\Models\Rule;
use Hubleto\App\Community\PriceList\Services\HistoryWriter;

/**
 * Copies a price list with its prices, rules and assignments. The copy is a draft with a new code.
 */
class CopyPriceList extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idPriceList = $this->router()->urlParamAsInteger('idPriceList');

    /** @var PriceList $mPriceList */
    $mPriceList = $this->getModel(PriceList::class);
    $source = $mPriceList->record->where($mPriceList->table . '.id', $idPriceList)->first();
    if (!$source) return ['status' => 'error', 'message' => $this->translate('Price list was not found.')];

    $header = $source->getAttributes();
    unset($header['id']);
    $header['code'] = $header['code'] . '-COPY-' . date('His');
    $header['name'] = $header['name'] . ' (' . $this->translate('copy') . ')';
    $header['status'] = PriceList::STATUS_DRAFT;
    $header['is_default'] = 0;
    $header['id_workflow'] = null;
    $header['id_workflow_step'] = null;
    $new = $mPriceList->record->recordCreate($header);
    $idNew = (int) $new['id'];

    foreach ([[Item::class, 'id_price_list'], [Rule::class, 'id_price_list'], [Assignment::class, 'id_price_list']] as [$modelClass, $fk]) {
      /** @var \Hubleto\Erp\Model $model */
      $model = $this->getModel($modelClass);
      foreach ($model->record->where($model->table . '.' . $fk, $idPriceList)->get() as $row) {
        $attributes = $row->getAttributes();
        unset($attributes['id']);
        $attributes[$fk] = $idNew;
        $model->record->recordCreate($attributes);
      }
    }

    /** @var HistoryWriter $historyWriter */
    $historyWriter = $this->getService(HistoryWriter::class);
    $historyWriter->write($idNew, History::EVENT_COPIED, $this->translate('Copied from price list') . ' ' . $source->code);

    return ['status' => 'success', 'idPriceList' => $idNew];
  }
}
