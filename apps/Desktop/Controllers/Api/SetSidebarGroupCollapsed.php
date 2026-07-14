<?php

namespace Hubleto\App\Community\Desktop\Controllers\Api;

class SetSidebarGroupCollapsed extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {

    $group = $this->router()->urlParamAsString("group");
    $isCollapsed = $this->router()->urlParamAsBool("isCollapsed");

    $sidebarGroupsCollapsed = $this->config()->getAsJson('sidebarGroupsCollapsed') ?? [];
    $sidebarGroupsCollapsed[$group] = $isCollapsed;

    $this->config()->save('sidebarGroupsCollapsed', json_encode($sidebarGroupsCollapsed));

    return [
      "status" => "success",
    ];
  }

}
