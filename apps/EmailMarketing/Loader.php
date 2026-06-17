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
      '/^email-marketing\/api\/import-emails\/?$/' => Controllers\Api\ImportEmails::class,
      '/^email-marketing\/api\/remove-all-recipients\/?$/' => Controllers\Api\RemoveAllRecipients::class,
      '/^email-marketing\/api\/send-test-email\/?$/' => Controllers\Api\SendTestEmail::class,
      '/^email-marketing\/api\/launch\/?$/' => Controllers\Api\Launch::class,

      '/^email-marketing\/?$/' => Controllers\Campaigns::class,

      '/^email-marketing\/campaigns(\/(?<recordId>\d+))?\/?$/' => Controllers\Campaigns::class,
      '/^email-marketing\/campaigns\/add?\/?$/' => ['controller' => Controllers\Campaigns::class, 'vars' => [ 'recordId' => -1 ]],

      '/^email-marketing\/emails(\/(?<recordId>\d+))?\/?$/' => Controllers\Emails::class,
      '/^email-marketing\/emails\/add?\/?$/' => ['controller' => Controllers\Emails::class, 'vars' => [ 'recordId' => -1 ]],

      '/^email-marketing\/recipients(\/(?<recordId>\d+))?\/?$/' => Controllers\EmailRecipients::class,
      '/^email-marketing\/recipients\/add?\/?$/' => ['controller' => Controllers\EmailRecipients::class, 'vars' => [ 'recordId' => -1 ]],
      '/^email-marketing\/recipients\/statuses(\/(?<recordId>\d+))?\/?$/' => Controllers\RecipientStatuses::class,
      '/^email-marketing\/recipients\/statuses\/add?\/?$/' => ['controller' => Controllers\RecipientStatuses::class, 'vars' => [ 'recordId' => -1 ]],

      '/^email-marketing\/click-tracker\/?$/' => Controllers\ClickTracker::class,
      '/^email-marketing\/email-preview\/?$/' => Controllers\EmailPreview::class,
      '/^email-marketing\/unsubscribe\/?$/' => Controllers\Unsubscribe::class,
    ]);

    /** @var \Hubleto\App\Community\Workflow\Manager */
    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflowGroup($this, 'email-marketing', Workflow::class);

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
      $this->getModel(Models\EmailRecipient::class)->upgradeSchema();
      $this->getModel(Models\EmailClick::class)->upgradeSchema();
      $this->getModel(Models\RecipientStatus::class)->upgradeSchema();
      $this->getModel(Models\CampaignSchedule::class)->upgradeSchema();
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
        <a class="btn btn-square btn-primary-outline" href="' . $this->env()->projectUrl . '/email-marketing/campaigns">
          <span class="icon"><i class="fas fa-users-viewfinder"></i></span>
          <span class="text">' . $this->translate('Campaigns') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/email-marketing/emails">
          <span class="icon"><i class="fas fa-envelope"></i></span>
          <span class="text">' . $this->translate('Emails') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/email-marketing/recipients">
          <span class="icon"><i class="fas fa-paper-plane"></i></span>
          <span class="text">' . $this->translate('Recipients') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/email-marketing/recipients/statuses">
          <span class="icon"><i class="fas fa-check-double"></i></span>
          <span class="text">' . $this->translate('Recipient statuses') . '</span>
        </a>
      </div>
    ';
  }

}
