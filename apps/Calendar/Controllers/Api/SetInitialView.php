<?php

namespace Hubleto\App\Community\Calendar\Controllers\Api;

class SetInitialView extends \Hubleto\Erp\Controllers\ApiController
{

  public function response(): array
  {

    /** @var \Hubleto\App\Community\Calendar\Loader */
    $calendarApp = $this->getService(\Hubleto\App\Community\Calendar\Loader::class);

    $calendarApp->setInitialView($this->router()->urlParamAsString('initialView'));

    return ['status' => 'success'];
  }

}
