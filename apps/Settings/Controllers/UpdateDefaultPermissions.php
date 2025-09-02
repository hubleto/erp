<?php

namespace Hubleto\App\Community\Settings\Controllers;

class UpdateDefaultPermissions extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'update-default-permissions', 'content' => $this->translate('Update default permissions') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    ob_start();
    $apps = $this->appManager()->getEnabledApps();
    array_walk($apps, function ($app) {
      echo $app->manifest['namespace'] . "\n";
      $app->assignPermissionsToRoles();
    });
    $this->viewParams['log'] = ob_get_clean();

    $this->setView('@Hubleto:App:Community:Settings/UpdateDefaultPermissions.twig');
  }

}
