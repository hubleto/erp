<?php

namespace Hubleto\App\Community\EmailMarketing;

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
      '/^email-marketing\/api\/save-recipients-from-contacts\/?$/' => Controllers\Api\SaveRecipientsFromContacts::class,
      '/^email-marketing\/api\/get-email-preview-info\/?$/' => Controllers\Api\GetEmailPreviewInfo::class,
      '/^email-marketing\/api\/get-email-test-info\/?$/' => Controllers\Api\GetEmailTestInfo::class,
      '/^email-marketing\/api\/get-email-launch-info\/?$/' => Controllers\Api\GetEmailLaunchInfo::class,
      '/^email-marketing\/api\/remove-recipient-from-email\/?$/' => Controllers\Api\RemoveRecipientFromEmail::class,
      '/^email-marketing\/api\/import-recipients\/?$/' => Controllers\Api\ImportRecipients::class,
      '/^email-marketing\/api\/remove-all-recipients\/?$/' => Controllers\Api\RemoveAllRecipients::class,
      '/^email-marketing\/api\/send-test-email\/?$/' => Controllers\Api\SendTestEmail::class,
      '/^email-marketing\/api\/launch\/?$/' => Controllers\Api\Launch::class,

      '/^email-marketing\/?$/' => Controllers\Home::class,

      '/^email-marketing\/campaigns(\/(?<recordId>\d+))?\/?$/' => Controllers\Campaigns::class,
      '/^email-marketing\/campaigns\/add?\/?$/' => ['controller' => Controllers\Campaigns::class, 'vars' => [ 'recordId' => -1 ]],

      '/^email-marketing\/campaigns\/schedules(\/(?<recordId>\d+))?\/?$/' => Controllers\CampaignsSchedules::class,
      '/^email-marketing\/campaigns\/schedules\/add?\/?$/' => ['controller' => Controllers\CampaignsSchedules::class, 'vars' => [ 'recordId' => -1 ]],

      '/^email-marketing\/campaigns\/schedules\/recipients(\/(?<recordId>\d+))?\/?$/' => Controllers\CampaignsSchedulesRecipients::class,
      '/^email-marketing\/campaigns\/schedules\/recipients\/add?\/?$/' => ['controller' => Controllers\CampaignsSchedulesRecipients::class, 'vars' => [ 'recordId' => -1 ]],

      '/^email-marketing\/emails(\/(?<recordId>\d+))?\/?$/' => Controllers\Emails::class,
      '/^email-marketing\/emails\/add?\/?$/' => ['controller' => Controllers\Emails::class, 'vars' => [ 'recordId' => -1 ]],

      '/^email-marketing\/emails\/clicks(\/(?<recordId>\d+))?\/?$/' => Controllers\EmailClicks::class,
      '/^email-marketing\/emails\/sent\/?$/' => Controllers\SentEmails::class,

      '/^email-marketing\/recipients(\/(?<recordId>\d+))?\/?$/' => Controllers\Recipients::class,
      '/^email-marketing\/recipients\/add?\/?$/' => ['controller' => Controllers\Recipients::class, 'vars' => [ 'recordId' => -1 ]],
      '/^email-marketing\/recipients\/statuses(\/(?<recordId>\d+))?\/?$/' => Controllers\RecipientStatuses::class,
      '/^email-marketing\/recipients\/statuses\/add?\/?$/' => ['controller' => Controllers\RecipientStatuses::class, 'vars' => [ 'recordId' => -1 ]],

      '/^email-marketing\/tags\/?$/' => Controllers\Tags::class,
      '/^email-marketing\/tags\/add\/?$/' => Controllers\Tags::class, 'vars' => [ 'recordId' => -1 ],

      '/^email-marketing\/click-tracker\/?$/' => Controllers\ClickTracker::class,
      '/^email-marketing\/email-preview\/?$/' => Controllers\EmailPreview::class,
      '/^email-marketing\/unsubscribe\/?$/' => Controllers\Unsubscribe::class,
    ]);

    /** @var \Hubleto\App\Community\Workflow\Manager */
    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflowGroup($this, 'email-marketing', Workflow::class);

    /** @var \Hubleto\App\Community\Settings\Loader $settingsApp */
    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Email marketing tags'),
      'icon' => 'fas fa-tags',
      'url' => 'email-marketing/tags',
    ]);

  }

  /**
   * [Description for installApp]
   *
   * @param int $round
   * 
   * @return void
   * 
   */
  public function installApp(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Campaign::class)->upgradeSchema();
      $this->getModel(Models\Email::class)->upgradeSchema();
      $this->getModel(Models\Recipient::class)->upgradeSchema();
      $this->getModel(Models\EmailClick::class)->upgradeSchema();
      $this->getModel(Models\RecipientStatus::class)->upgradeSchema();
      $this->getModel(Models\CampaignSchedule::class)->upgradeSchema();
      $this->getModel(Models\CampaignScheduleRecipient::class)->upgradeSchema();
      $this->getModel(Models\CampaignTag::class)->upgradeSchema();
      $this->getModel(Models\Tag::class)->upgradeSchema();
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
      ' . $this->secondSidebarTitle() . '
      <div class="app-sidebar-buttons">
        ' . $this->secondSidebarButton('email-marketing/campaigns', 'fas fa-users-viewfinder', 'Campaigns') . '
        ' . $this->secondSidebarButton('email-marketing/emails', 'fas fa-envelope', 'Emails') . '
        ' . $this->secondSidebarButton('email-marketing/emails/sent', 'fas fa-arrow-right-from-bracket', 'Sent') . '
        ' . $this->secondSidebarButton('email-marketing/emails/clicks', 'fas fa-hand-pointer', 'Clicks') . '
        ' . $this->secondSidebarButton('email-marketing/recipients', 'fas fa-paper-plane', 'Recipients') . '
        ' . $this->secondSidebarButton('email-marketing/recipients/statuses', 'fas fa-check-double', 'Recipient statuses') . '
        ' . $this->secondSidebarButton('email-marketing/tags', 'fas fa-tag', 'Tags') . '
      </div>
    ';
  }

}
