<?php

namespace HubletoApp\Community\About;

class Loader extends \Hubleto\Framework\App
{
  public bool $canBeDisabled = false;
  public bool $permittedForAllUsers = true;

  public function init(): void
  {
    parent::init();
    $this->main->router->httpGet([ '/^about\/?$/' => Controllers\About::class ]);
  }

}
