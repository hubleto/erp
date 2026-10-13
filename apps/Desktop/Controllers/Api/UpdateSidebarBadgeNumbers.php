<?php

namespace Hubleto\App\Community\Desktop\Controllers\Api;

use Hubleto\App\Community\Desktop\Services\Sidebar;

class UpdateSidebarBadgeNumbers extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {

    /** @var Sidebar */
    $sidebarService = $this->getService(Sidebar::class);

    $sidebarService->invalidateCache();

    return [
      "sidebarBadgeNumbers" => $sidebarService->getBadgeNumbers(),
    ];
  }

}
