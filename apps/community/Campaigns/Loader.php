<?php

namespace HubletoApp\Community\Campaigns;

class Loader extends \HubletoMain\Core\App
{

  public bool $hasCustomSettings = true;

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^campaigns\/?$/' => Controllers\Campaigns::class,
    ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\Campaign($this->main))->dropTableIfExists()->install();
    }
  }

}