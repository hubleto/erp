<?php

namespace HubletoMain;

use Hubleto\Framework\Interfaces\AppManagerInterface;
use Hubleto\Framework\Router;

/**
 * Core implementation of model.
 */
class Model extends \Hubleto\Framework\Models\Model
{

  /**
   * [Description for getAppManager]
   *
   * @return AppManagerInterface
   * 
   */
  public function getAppManager(): AppManagerInterface
  {
    return $this->main->getAppManager();
  }

  /**
   * [Description for getRouter]
   *
   * @return Router
   * 
   */
  public function getRouter(): Router
  {
    return $this->main->getRouter();
  }

  //////////////////////////////////////////////////////////////////
  // callbacks

  /**
   * onBeforeCreate
   * @param array<string, mixed> $record
   * @return array<string, mixed>
   */
  public function onBeforeCreate(array $record): array
  {
    $this->main->hooks->run('model:on-before-create', ['model' => $this, 'record' => $record]);
    return $record;
  }

  /**
   * onBeforeUpdate
   * @param array<string, mixed> $record
   * @return array<string, mixed>
   */
  public function onBeforeUpdate(array $record): array
  {
    $this->main->hooks->run('model:on-before-update', ['model' => $this, 'record' => $record]);
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
    $this->main->hooks->run('model:on-after-create', ['model' => $this, 'savedRecord' => $savedRecord]);
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
    $this->main->hooks->run('model:on-after-update', [
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
    $this->main->hooks->run('model:on-before-delete', ['model' => $this, 'id' => $id]);
    return $id;
  }

  /**
   * onAfterDelete
   * @param int $id
   * @return int
   */
  public function onAfterDelete(int $id): int
  {
    $this->main->hooks->run('model:on-after-delete', ['model' => $this, 'id' => $id]);
    return $id;
  }

}
