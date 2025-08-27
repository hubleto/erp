<?php

namespace HubletoApp\Community\Settings\Controllers;

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
    $this->viewParams['permissions'] = ($this->getModel(\HubletoApp\Community\Settings\Models\Permission::class))->record->orderBy('permission')->get();
    $this->setView('@HubletoApp:Community:Settings/Permissions.twig');
  }

}
