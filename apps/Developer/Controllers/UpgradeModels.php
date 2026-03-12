<?php

namespace Hubleto\App\Community\Developer\Controllers;

use Hubleto\Framework\Enums\InstalledMigrationEnum;
use Hubleto\Framework\Interfaces\ModelInterface;

class UpgradeModels extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'developer', 'content' => $this->translate('Developer tools') ],
      [ 'url' => '', 'content' => $this->translate('Upgrade models') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:Developer/UpgradeModels.twig');
  }

}
