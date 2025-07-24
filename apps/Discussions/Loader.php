<?php

namespace HubletoApp\Community\Discussions;

class Loader extends \Hubleto\Framework\App
{
  public array $externalModels = [];

  // init
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^discussions(\/(?<recordId>\d+))?\/?$/' => Controllers\Discussions::class,
      '/^discussions\/members(\/(?<recordId>\d+))?\/?$/' => Controllers\Members::class,
      '/^discussions\/messages(\/(?<recordId>\d+))?\/?$/' => Controllers\Messages::class,

      '/^discussions\/api\/send-message\/?$/' => Controllers\Api\SendMessage::class,
    ]);

    $appMenu = $this->main->apps->community('Desktop')->appMenu;
    $appMenu->addItem($this, 'discussions', $this->translate('Discussions'), 'fas fa-user');
    $appMenu->addItem($this, 'discussions/members', $this->translate('Members'), 'fas fa-file-import');
    $appMenu->addItem($this, 'discussions/messages', $this->translate('Messages'), 'fas fa-file-import');
  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\Discussion($this->main))->dropTableIfExists()->install();
      (new Models\Member($this->main))->dropTableIfExists()->install();
      (new Models\Message($this->main))->dropTableIfExists()->install();
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
