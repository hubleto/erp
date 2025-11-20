<?php declare(strict_types=1);

namespace Hubleto\App\Community\Auth\Controllers;

class NotEnoughPermissions extends \Hubleto\Framework\Controllers\NotEnoughPermissions
{
  public bool $requiresAuthenticatedUser = false;
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Auth/NotEnoughPermissions.twig');
  }

}
