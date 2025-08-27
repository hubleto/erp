<?php

namespace HubletoApp\Community\Campaigns;

class Loader extends \Hubleto\Framework\App
{
  public bool $hasCustomSettings = true;

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->getRouter()->httpGet([
      '/^campaigns\/api\/save-contacts\/?$/' => Controllers\Api\SaveContacts::class,
      '/^campaigns(\/(?<recordId>\d+))?\/?$/' => Controllers\Campaigns::class,
    ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Campaign::class)->dropTableIfExists()->install();
      $this->getModel(Models\CampaignContact::class)->dropTableIfExists()->install();
      $this->getModel(Models\CampaignTask::class)->dropTableIfExists()->install();
    }
  }

}
