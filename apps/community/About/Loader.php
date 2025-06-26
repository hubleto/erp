<?php

namespace HubletoApp\Community\About;

class Loader extends \HubletoMain\Core\App
{

  public bool $canBeDisabled = false;

  public function init(): void
  {
    parent::init();
    $this->main->router->httpGet([ '/^about\/?$/' => Controllers\About::class ]);
  }

}