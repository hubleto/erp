<?php

namespace HubletoApp\Community\Messages;

class Loader extends \HubletoMain\Core\App
{

  public bool $hasCustomSettings = true;

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
  }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^messages(\/(?<recordId>\d+))?\/?$/' => Controllers\Messages::class,
      '/^messages\/settings\/?$/' => Controllers\Settings::class,
    ]);
  }


  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mMessage = new Models\Message($this->main);
      $mMessage->dropTableIfExists()->install();
    }
  }

  public function installDefaultPermissions(): void
  {
  }
}
