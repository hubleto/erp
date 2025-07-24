<?php

namespace HubletoApp\Community\Tasks;

class Loader extends \Hubleto\Framework\App
{
  public array $externalModels = [];

  // init
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^tasks(\/(?<recordId>\d+))?\/?$/' => Controllers\Tasks::class,
    ]);

  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\Task($this->main))->dropTableIfExists()->install();
    }
    if ($round == 2) {
      // do something in the 2nd round, if required
    }
    if ($round == 3) {
      // do something in the 3rd round, if required
    }
  }

  public function registerExternalModel(\Hubleto\Framework\App $app, string $modelClass)
  {
    $this->externalModels[$modelClass] = $app;
  }

  public function getRegisteredExternalModels(): array
  {
    return $this->externalModels;
  }

}
