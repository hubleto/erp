<?php declare(strict_types=1);

namespace Hubleto\Erp\Controllers;

use Hubleto\Framework\Controllers\Controller;

class NotFound extends \Hubleto\Erp\Controller
{
  public bool $requiresAuthenticatedUser = false;
  public bool $hideDefaultDesktop = true;
  public string $translationContext = 'Hubleto\\Erp\\Loader';
  public string $translationContextInner = 'Controllers\\NotFound';

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@hubleto-main/NotFound.twig');
  }

}
