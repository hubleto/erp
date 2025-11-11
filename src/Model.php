<?php

namespace Hubleto\Erp;

use Hubleto\Framework\Router;

/**
 * Core implementation of model.
 */
class Model extends \Hubleto\Framework\Model
{

  const COLUMN_ID_CUSTOMER_DEFAULT_ICON = 'fas fa-address-card bg-yellow-50 rounded text-yellow-600 p-2 mr-2 w-10 text-center block';
  const COLUMN_CONTACT_DEFAULT_ICON = 'fas fa-id-badge bg-yellow-50 rounded text-yellow-600 p-2 mr-2 w-10 text-center block';
  CONST COLUMN_IDENTIFIER_DEFUALT_ICON = 'fas fa-pen bg-blue-50 rounded text-blue-600 p-2 mr-2 w-10 text-center block';
  const COLUMN_NAME_DEFAULT_ICON = 'fas fa-a bg-sky-50 rounded text-sky-600 p-2 mr-2 w-10 text-center block';
  const COLUMN_ADDRESS_DEFAULT_ICON = 'fas fa-location-dot bg-green-50 rounded text-green-600 p-2 mr-2 w-10 text-center block';
  const COLUMN_COLOR_DEFAULT_ICON = 'fas fa-palette bg-violet-50 rounded text-violet-600 p-2 mr-2 w-10 text-center block';

  //////////////////////////////////////////////////////////////////
  // callbacks

  /**
   * onBeforeCreate
   * @param array<string, mixed> $record
   * @return array<string, mixed>
   */
  public function onBeforeCreate(array $record): array
  {
    $this->hookManager()->run('model:on-before-create', ['model' => $this, 'record' => $record]);
    return $record;
  }

  /**
   * onBeforeUpdate
   * @param array<string, mixed> $record
   * @return array<string, mixed>
   */
  public function onBeforeUpdate(array $record): array
  {
    $this->hookManager()->run('model:on-before-update', ['model' => $this, 'record' => $record]);
    return $record;
  }

  /**
   * onAfterCreate
   * @param array<string, mixed> $originalRecord
   * @param array<string, mixed> $savedRecord
   * @return array<string, mixed>
   */
  public function onAfterCreate(array $savedRecord): array
  {
    $this->hookManager()->run('model:on-after-create', ['model' => $this, 'savedRecord' => $savedRecord]);
    return $savedRecord;
  }

  /**
   * onAfterUpdate
   * @param array<string, mixed> $originalRecord
   * @param array<string, mixed> $savedRecord
   * @return array<string, mixed>
   */
  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $this->hookManager()->run('model:on-after-update', [
      'model' => $this,
      'originalRecord' => $originalRecord,
      'savedRecord' => $savedRecord
    ]);
    return $savedRecord;
  }

  /**
   * onBeforeDelete
   * @param int $id
   * @return int
   */
  public function onBeforeDelete(int $id): int
  {
    $this->hookManager()->run('model:on-before-delete', ['model' => $this, 'id' => $id]);
    return $id;
  }

  /**
   * onAfterDelete
   * @param int $id
   * @return int
   */
  public function onAfterDelete(int $id): int
  {
    $this->hookManager()->run('model:on-after-delete', ['model' => $this, 'id' => $id]);
    return $id;
  }

}
