<?php

namespace Hubleto\App\Community\Campaigns;

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

    $this->router()->get([
      '/^campaigns\/api\/save-contacts\/?$/' => Controllers\Api\SaveContacts::class,
      '/^campaigns\/api\/get-mail-preview-info\/?$/' => Controllers\Api\GetMailPreviewInfo::class,
      '/^campaigns(\/(?<recordId>\d+))?\/?$/' => Controllers\Campaigns::class,
      '/^campaigns\/tracker\/?$/' => Controllers\Tracker::class,
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
