<?php

namespace Hubleto\App\Community\Workflow\Automats\Actions;

use Hubleto\App\Community\Workflow\Interfaces\AutomatActionInterface;
use Hubleto\Erp\Core;

class UpdateRecord extends Core implements AutomatActionInterface
{

  /**
   * [Description for execute]
   *
   * @param array $arguments
   * 
   * @return void
   * 
   */
  public function execute(array $arguments): void
  {
    $updatedModel = (string) $arguments['updatedModel'] ?? '';
    $updatedRecord = (array) $arguments['updatedRecord'] ?? [];
    $column = (string) $arguments['column'] ?? '';
    $value = $arguments['value'] ?? null;

    $modelObject = $this->getModel($updatedModel);

    $modelObject->record->where('id', $updatedRecord['id'] ?? 0)->update([
      $column => $value,
    ]);
  }
}