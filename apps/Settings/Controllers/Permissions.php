<?php

namespace Hubleto\App\Community\Settings\Controllers;

class Permissions extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'permissions', 'content' => $this->translate('Permissions') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['permissions'] = ($this->getModel(\Hubleto\App\Community\Settings\Models\Permission::class))->record->orderBy('permission')->get();
    $this->setView('@Hubleto:App:Community:Settings/Permissions.twig');
  }

}
