<?php

namespace Hubleto\App\Community\Campaigns;

class Loader extends \Hubleto\Framework\App
{

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
      '/^campaigns\/api\/get-campaign-warnings\/?$/' => Controllers\Api\GetCampaignWarnings::class,
      '/^campaigns\/api\/send-test-email-to-me\/?$/' => Controllers\Api\SendTestEmailToMe::class,
      '/^campaigns\/api\/launch\/?$/' => Controllers\Api\Launch::class,
      '/^campaigns(\/(?<recordId>\d+))?\/?$/' => Controllers\Campaigns::class,
      '/^campaigns\/add?\/?$/' => ['controller' => Controllers\Campaigns::class, 'vars' => [ 'recordId' => -1 ]],
      '/^campaigns\/click-tracker\/?$/' => Controllers\ClickTracker::class,
    ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Campaign::class)->dropTableIfExists()->install();
      $this->getModel(Models\Recipient::class)->dropTableIfExists()->install();
      $this->getModel(Models\CampaignTask::class)->dropTableIfExists()->install();
      $this->getModel(Models\Click::class)->dropTableIfExists()->install();
    }
  }

}
