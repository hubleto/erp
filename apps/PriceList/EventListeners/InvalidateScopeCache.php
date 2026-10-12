<?php

namespace Hubleto\App\Community\PriceList\EventListeners;

use Hubleto\App\Community\Customers\Models\CustomerTag;
use Hubleto\App\Community\Products\Models\Category;
use Hubleto\App\Community\Products\Models\Group;
use Hubleto\App\Community\PriceList\Services\PriceResolver;
use Hubleto\Framework\Interfaces\ModelInterface;

/**
 * Clears cached categories, groups and customer tags of the price resolver when they change within a request.
 */
class InvalidateScopeCache extends \Hubleto\Framework\EventListener implements \Hubleto\Framework\Interfaces\EventListenerInterface
{
  public function onModelAfterCreate(ModelInterface $model, array $savedRecord): void
  {
    if (!$this->isScopeModel($model)) return;
    $this->clear();
  }

  public function onModelAfterUpdate(ModelInterface $model, array $originalRecord, array $savedRecord): void
  {
    if (!$this->isScopeModel($model)) return;
    $this->clear();
  }

  public function onModelAfterDelete(ModelInterface $model, int $id): void
  {
    if (!$this->isScopeModel($model)) return;
    $this->clear();
  }

  protected function isScopeModel(ModelInterface $model): bool
  {
    return $model instanceof Category || $model instanceof Group || $model instanceof CustomerTag;
  }

  protected function clear(): void
  {
    /** @var PriceResolver $resolver */
    $resolver = $this->getService(PriceResolver::class);
    $resolver->clearCache();
  }
}
