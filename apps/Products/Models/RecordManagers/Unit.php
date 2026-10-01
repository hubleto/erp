<?php

namespace Hubleto\App\Community\Products\Models\RecordManagers;

class Unit extends \Hubleto\Erp\RecordManager
{
  public $table = 'product_units';

  // A form that lets you pick a unit usually wants to show what that choice implies before the
  // record is saved - the default lookup row carries only the label.
  public function prepareLookupData(array $dataRaw): array
  {
    $data = parent::prepareLookupData($dataRaw);

    foreach ($dataRaw as $key => $value) {
      if (isset($data[$key]) && isset($value['tare_weight'])) $data[$key]['tare_weight'] = (float) $value['tare_weight'];
    }

    return $data;
  }
}
