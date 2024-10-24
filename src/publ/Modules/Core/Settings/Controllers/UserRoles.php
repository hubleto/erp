<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Controllers;

class UserRoles extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->app->translate('Settings') ],
      [ 'url' => 'user-roles', 'content' => $this->app->translate('User Roles') ],
    ]);
  }
 }