<?php

namespace HubletoApp\Community\Settings\Controllers;

class Permissions extends \HubletoMain\Controller
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
    $this->viewParams['permissions'] = (new \HubletoApp\Community\Settings\Models\Permission($this->main))->record->orderBy('permission')->get();
    $this->setView('@HubletoApp:Community:Settings/Permissions.twig');
  }

}
