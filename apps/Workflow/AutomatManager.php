<?php

namespace Hubleto\App\Community\Workflow;

use Hubleto\App\Community\Workflow\Models\Automat;

class AutomatManager extends \Hubleto\Erp\Core
{

  public static function getAutomats(string $forTrigger): array
  {
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $mAutomat = $hubleto->getService(Automat::class);
    // $automats = $hubleto->config()->get('workflowAutomats');
    // if (is_string($automats)) $automats = @json_decode($automats, true);
    // if (!is_array($automats)) $automats = [];

    // return array_filter(
    //   $automats,
    //   function($v, $k) use ($forTrigger) { return $v['trigger'] == $forTrigger; },
    //   ARRAY_FILTER_USE_BOTH
    // );

    $automats = $mAutomat->record->where('trigger', $forTrigger)->orderBy('execution_order')->get()->toArray();
    foreach ($automats as $key => $automat) {
      $automats[$key]['conditions'] = @json_decode($automat['conditions'], true);
      $automats[$key]['actions'] = @json_decode($automat['actions'], true);
    }

    return $automats;
  }
}
