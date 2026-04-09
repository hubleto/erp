<?php

namespace Hubleto\App\Community\Campaigns;

class Loader extends \Hubleto\Erp\App
{

  /**
   * Inits the app: adds routes, settings, calendars, event listeners, menu items, ...
   *
   * @return void
   *
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^campaigns\/api\/save-recipients-from-contacts\/?$/' => Controllers\Api\SaveRecipientsFromContacts::class,
      '/^campaigns\/api\/get-mail-preview-info\/?$/' => Controllers\Api\GetMailPreviewInfo::class,
      '/^campaigns\/api\/get-campaign-test-info\/?$/' => Controllers\Api\GetCampaignTestInfo::class,
      '/^campaigns\/api\/get-campaign-launch-info\/?$/' => Controllers\Api\GetCampaignLaunchInfo::class,
      '/^campaigns\/api\/import-emails\/?$/' => Controllers\Api\ImportEmails::class,
      '/^campaigns\/api\/send-test-email\/?$/' => Controllers\Api\SendTestEmail::class,
      '/^campaigns\/api\/launch\/?$/' => Controllers\Api\Launch::class,
      '/^leads\/api\/log-activity\/?$/' => Controllers\Api\LogActivity::class,

      '/^campaigns(\/(?<recordId>\d+))?\/?$/' => Controllers\Campaigns::class,
      '/^campaigns\/add?\/?$/' => ['controller' => Controllers\Campaigns::class, 'vars' => [ 'recordId' => -1 ]],
      '/^campaigns\/recipients(\/(?<recordId>\d+))?\/?$/' => Controllers\Recipients::class,
      '/^campaigns\/recipients\/add?\/?$/' => ['controller' => Controllers\Recipients::class, 'vars' => [ 'recordId' => -1 ]],
      '/^campaigns\/recipients\/statuses(\/(?<recordId>\d+))?\/?$/' => Controllers\RecipientStatuses::class,
      '/^campaigns\/recipients\/statuses\/add?\/?$/' => ['controller' => Controllers\RecipientStatuses::class, 'vars' => [ 'recordId' => -1 ]],
      '/^campaigns\/click-tracker\/?$/' => Controllers\ClickTracker::class,
      '/^campaigns\/mail-preview\/?$/' => Controllers\MailPreview::class,
      '/^campaigns\/unsubscribe\/?$/' => Controllers\Unsubscribe::class,
    ]);

    /** @var \Hubleto\App\Community\Calendar\Manager */
    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'campaigns', Calendar::class);

    /** @var \Hubleto\App\Community\Workflow\Manager */
    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflow($this, 'campaigns', Workflow::class);

  }

  public function installApp(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Campaign::class)->upgradeSchema();
      $this->getModel(Models\Recipient::class)->upgradeSchema();
      $this->getModel(Models\RecipientStatus::class)->upgradeSchema();
      $this->getModel(Models\CampaignTask::class)->upgradeSchema();
      $this->getModel(Models\CampaignActivity::class)->upgradeSchema();
      $this->getModel(Models\Click::class)->upgradeSchema();
    }
  }

  /**
   * [Description for renderSecondSidebar]
   *
   * @return string
   *
   */
  public function renderSecondSidebar(): string
  {
    return '
      <div class="flex flex-col gap-2">
        <a class="btn btn-square btn-primary-outline" href="' . $this->env()->projectUrl . '/campaigns">
          <span class="icon"><i class="fas fa-users-viewfinder"></i></span>
          <span class="text">' . $this->translate('Campaigns') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/campaigns/recipients">
          <span class="icon"><i class="fas fa-paper-plane"></i></span>
          <span class="text">' . $this->translate('Recipients') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/campaigns/recipients/statuses">
          <span class="icon"><i class="fas fa-check-double"></i></span>
          <span class="text">' . $this->translate('Recipient statuses') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/calendar?show=campaigns">
          <span class="icon"><i class="fas fa-calendar-days"></i></span>
          <span class="text">' . $this->translate('Calendar') . '</span>
        </a>
      </div>
    ';
  }

}
