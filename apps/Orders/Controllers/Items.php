<?php

namespace Hubleto\App\Community\Orders\Controllers;

use Hubleto\App\Community\Orders\Counter;

class Items extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Items') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $counter = new Counter();

    $this->viewParams['dueAndChargeableItemsNotPreparedForInvoice'] = $counter
      ->queryForDueAndChargeableItemsNotPreparedForInvoice()
      // ->with('CURRENCY')
      ->get();

      // var_dump($this->viewParams['dueAndChargeableItemsNotPreparedForInvoice']->toArray());exit;

    $this->setView('@Hubleto:App:Community:Orders/Items.twig');
  }
}
