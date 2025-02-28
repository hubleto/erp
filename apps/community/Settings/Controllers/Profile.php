<?php

namespace HubletoApp\Community\Settings\Controllers;

class Profile extends \HubletoMain\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'profiles', 'content' => $this->translate('Profile') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Settings/Profile.twig');
  }

}