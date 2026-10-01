<?php

namespace Hubleto\App\Custom\LinkedinMessages;

use Hubleto\App\Community\Auth\Models\User;

class Loader extends \Hubleto\Erp\App
{

  /**
   * Inits the app: adds routes, settings, calendars, workflows, boards, crons, menu items, ...
   *
   * @return void
   *
   */
  public function init(): void
  {
    parent::init();

    // CRUD routes: one per model
    $this->router()->crud('linkedin-messages', Controllers\Messages::class);
    $this->router()->crud('linkedin-messages/profiles', Controllers\Profiles::class);
    $this->router()->crud('linkedin-messages/accounts', Controllers\Accounts::class);
    $this->router()->crud('linkedin-messages/campaigns', Controllers\Campaigns::class);
    $this->router()->crud('linkedin-messages/tags', Controllers\Tags::class);
    $this->router()->crud('linkedin-messages/replies', Controllers\Replies::class);
    $this->router()->crud('linkedin-messages/followups', Controllers\MessageActivities::class);

    // non-CRUD routes
    $this->router()->get([
      '/^linkedin-messages\/api\/oauth-connect\/?$/' => Controllers\Api\OauthConnect::class,
      '/^linkedin-messages\/api\/oauth-callback\/?$/' => Controllers\Api\OauthCallback::class,
      '/^linkedin-messages\/api\/disconnect\/?$/' => Controllers\Api\Disconnect::class,
      '/^linkedin-messages\/api\/sync\/?$/' => Controllers\Api\Sync::class,
      '/^linkedin-messages\/api\/set-read\/?$/' => Controllers\Api\SetRead::class,
      '/^linkedin-messages\/api\/send-reply\/?$/' => Controllers\Api\SendReply::class,
      '/^linkedin-messages\/api\/mark-reply-sent\/?$/' => Controllers\Api\MarkReplySent::class,
      '/^linkedin-messages\/api\/log-activity\/?$/' => Controllers\Api\LogActivity::class,

      '/^linkedin-messages\/board\/unread-messages\/?$/' => Controllers\Boards\UnreadMessages::class,
      '/^linkedin-messages\/board\/messages-without-followup\/?$/' => Controllers\Boards\MessagesWithoutFollowup::class,

      '/^linkedin-messages\/settings\/?$/' => Controllers\Settings::class,
    ]);

    /** @var \Hubleto\App\Community\Settings\Loader $settingsApp */
    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('LinkedIn: connection settings'),
      'icon' => 'fab fa-linkedin',
      'url' => 'linkedin-messages/settings',
    ]);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('LinkedIn: message tags'),
      'icon' => 'fas fa-tags',
      'url' => 'linkedin-messages/tags',
    ]);

    /** @var \Hubleto\App\Community\Calendar\Manager $calendarManager */
    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'linkedin-messages', Calendar::class);

    /** @var \Hubleto\App\Community\Workflow\Manager $workflowManager */
    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflowGroup($this, 'linkedin-campaigns', Workflow::class);

    /** @var \Hubleto\App\Community\Dashboards\Manager $boards */
    $boards = $this->getService(\Hubleto\App\Community\Dashboards\Manager::class);
    $boards->addBoard($this, $this->translate('Unread LinkedIn messages'), 'linkedin-messages/board/unread-messages');
    $boards->addBoard($this, $this->translate('LinkedIn messages without follow-up'), 'linkedin-messages/board/messages-without-followup');

    /** @var \Hubleto\App\Community\Desktop\AppMenuManager $appMenu */
    $appMenu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'linkedin-messages', $this->translate('LinkedIn messages'), 'fab fa-linkedin');
    $appMenu->addItem($this, 'linkedin-messages/followups', $this->translate('LinkedIn follow-ups'), 'fas fa-calendar-check');

    // periodic synchronization of messages
    $this->cronManager()->addCron(Crons\SyncMessages::class);
  }

  /**
   * Creates tables and sample data.
   *
   * @param int $round
   *
   * @return void
   *
   */
  public function installApp(int $round): void
  {
    if ($round == 1) {
      $mTag = $this->getModel(Models\Tag::class);
      $mAccount = $this->getModel(Models\Account::class);
      $mProfile = $this->getModel(Models\Profile::class);
      $mCampaign = $this->getModel(Models\Campaign::class);
      $mMessage = $this->getModel(Models\Message::class);
      $mMessageTag = $this->getModel(Models\MessageTag::class);
      $mReply = $this->getModel(Models\Reply::class);
      $mMessageActivity = $this->getModel(Models\MessageActivity::class);

      $mTag->upgradeSchema();
      $mAccount->upgradeSchema();
      $mProfile->upgradeSchema();
      $mCampaign->upgradeSchema();
      $mMessage->upgradeSchema();
      $mMessageTag->upgradeSchema();
      $mReply->upgradeSchema();
      $mMessageActivity->upgradeSchema();

      $mTag->record->recordCreate([ 'name' => $this->translate('Interested'), 'color' => '#4caf50' ]);
      $mTag->record->recordCreate([ 'name' => $this->translate('Needs answer'), 'color' => '#f44336' ]);
      $mTag->record->recordCreate([ 'name' => $this->translate('Recruiter'), 'color' => '#2196f3' ]);
      $mTag->record->recordCreate([ 'name' => $this->translate('Partnership'), 'color' => '#9c27b0' ]);
      $mTag->record->recordCreate([ 'name' => $this->translate('Spam'), 'color' => '#9e9e9e' ]);

      // sample workflow for campaigns
      /** @var \Hubleto\App\Community\Workflow\Models\Workflow $mWorkflow */
      $mWorkflow = $this->getModel(\Hubleto\App\Community\Workflow\Models\Workflow::class);
      /** @var \Hubleto\App\Community\Workflow\Models\WorkflowStep $mWorkflowStep */
      $mWorkflowStep = $this->getModel(\Hubleto\App\Community\Workflow\Models\WorkflowStep::class);

      $idWorkflow = $mWorkflow->record->recordCreate([
        'name' => $this->translate('LinkedIn campaigns'),
        'show_in_kanban' => 1,
        'order' => 20,
        'group' => 'linkedin-campaigns',
      ])['id'];
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Planning'), 'order' => 1, 'color' => '#344556', 'tag' => 'linkedin-campaign-planning']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Outreach'), 'order' => 2, 'color' => '#0a66c2', 'tag' => 'linkedin-campaign-outreach']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Follow-up'), 'order' => 3, 'color' => '#d8a082', 'tag' => 'linkedin-campaign-followup']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Evaluation'), 'order' => 4, 'color' => '#6830a5', 'tag' => 'linkedin-campaign-evaluation']);
      $mWorkflowStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Done'), 'order' => 5, 'color' => '#008000', 'tag' => 'linkedin-campaign-done']);
    }
  }

  /**
   * Generates demo data.
   *
   * @return void
   *
   */
  public function generateDemoData(): void
  {
    /** @var Models\Account $mAccount */
    $mAccount = $this->getModel(Models\Account::class);
    $mProfile = $this->getModel(Models\Profile::class);
    $mCampaign = $this->getModel(Models\Campaign::class);
    $mTag = $this->getModel(Models\Tag::class);
    $mMessage = $this->getModel(Models\Message::class);
    $mMessageTag = $this->getModel(Models\MessageTag::class);
    $mReply = $this->getModel(Models\Reply::class);
    $mMessageActivity = $this->getModel(Models\MessageActivity::class);

    $idOwner = (int) ($this->getModel(User::class)->record->orderBy('id')->first()?->id ?? 1);
    $tagIds = $mTag->record->pluck('id')->toArray();

    // demo account has no tokens, so it is skipped by the synchronization
    $idAccount = $mAccount->record->recordCreate([
      'id_owner' => $idOwner,
      'linkedin_member_id' => 'demo-member',
      'name' => 'Demo Account',
      'headline' => $this->translate('Head of Marketing at Demo company'),
      'email' => 'demo.account@example.com',
      'profile_url' => 'https://www.linkedin.com/in/demo-account',
      'locale' => 'en_US',
      'scopes' => Client::DEFAULT_SCOPES,
      'status' => Models\Account::STATUS_CONNECTED,
      'last_sync_at' => date('Y-m-d H:i:s'),
      'last_sync_info' => $this->translate('Demo data, not connected to LinkedIn.'),
      'changelog_cursor' => (string) (time() * 1000),
    ])['id'];

    $idCampaign1 = $mCampaign->record->recordCreate([
      'name' => $this->translate('Webinar invitations Q4'),
      'target' => $this->translate('Marketing managers in mid-size SaaS companies'),
      'goal' => $this->translate('Get 50 registrations for the product webinar'),
      'id_owner' => $idOwner,
    ])['id'];
    $idCampaign2 = $mCampaign->record->recordCreate([
      'name' => $this->translate('Partner program outreach'),
      'target' => $this->translate('Agencies and consultants'),
      'goal' => $this->translate('Sign 5 new partners'),
      'id_owner' => $idOwner,
    ])['id'];

    $people = [
      ['Anna', 'Novak', 'Marketing Manager', 'Acme SaaS'],
      ['Peter', 'Horvath', 'CEO', 'Digital Agency Ltd.'],
      ['Maria', 'Kovacova', 'Talent Acquisition Specialist', 'Hiring Experts'],
      ['John', 'Smith', 'Head of Partnerships', 'CloudWorks'],
      ['Eva', 'Mala', 'Growth Consultant', 'Freelance'],
      ['Tomas', 'Vrabel', 'Sales Director', 'Industrial Group'],
    ];
    $profileIds = [];
    foreach ($people as $i => $person) {
      $profileIds[] = $mProfile->record->recordCreate([
        'id_owner' => $idOwner,
        'linkedin_urn' => 'urn:li:person:demo' . ($i + 1),
        'first_name' => $person[0],
        'last_name' => $person[1],
        'headline' => $person[2],
        'company' => $person[3],
        'profile_url' => 'https://www.linkedin.com/in/demo-' . strtolower($person[0] . '-' . $person[1]),
      ])['id'];
    }

    $texts = [
      $this->translate('Hi, thanks for the invitation. Could you send me more details about the webinar?'),
      $this->translate('We are looking for a reliable partner for a joint project. Do you have time for a call next week?'),
      $this->translate('I have an interesting opportunity that matches your profile. Are you open to new roles?'),
      $this->translate('Thanks for connecting! What does your partner program look like?'),
      $this->translate('Sounds good, please send me the pricing.'),
      $this->translate('Not interested at the moment, maybe next year.'),
    ];

    foreach ($texts as $i => $text) {
      $isUnread = $i < 3;
      $idMessage = $mMessage->record->recordCreate([
        'id_account' => $idAccount,
        'id_profile' => $profileIds[$i],
        'id_campaign' => $i % 2 == 0 ? $idCampaign1 : $idCampaign2,
        'id_owner' => $idOwner,
        'linkedin_message_id' => 'demo-message-' . ($i + 1),
        'thread_id' => 'demo-thread-' . ($i + 1),
        'direction' => Models\Message::DIRECTION_INCOMING,
        'subject' => '',
        'body' => $text,
        'sent_at' => date('Y-m-d H:i:s', strtotime('-' . ($i + 1) . ' days')),
        'is_read' => $isUnread ? 0 : 1,
      ])['id'];

      if (count($tagIds) > 0) {
        $mMessageTag->record->recordCreate(['id_message' => $idMessage, 'id_tag' => $tagIds[$i % count($tagIds)]]);
      }

      if ($i == 3 || $i == 4) {
        $mMessageActivity->record->recordCreate([
          'id_message' => $idMessage,
          'subject' => $this->translate('Follow up on LinkedIn conversation'),
          'date_start' => date('Y-m-d', strtotime('+' . ($i - 1) . ' days')),
          'time_start' => '10:00:00',
          'date_end' => date('Y-m-d', strtotime('+' . ($i - 1) . ' days')),
          'time_end' => '10:15:00',
          'all_day' => 0,
          'completed' => 0,
          'id_owner' => $idOwner,
        ]);
      }

      if ($i == 4) {
        $mReply->record->recordCreate([
          'id_message' => $idMessage,
          'id_owner' => $idOwner,
          'body' => $this->translate('Thank you! I am sending the pricing by email today.'),
          'status' => Models\Reply::STATUS_MARKED_SENT,
          'sent_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
        ]);
      }
    }
  }

  /**
   * Number of unread incoming messages shown as badge in the sidebar.
   *
   * @return int
   *
   */
  public function getSidebarBadgeNumber(): int
  {
    /** @var Counter */
    $counter = $this->getService(Counter::class);
    return $counter->unreadMessages();
  }

  /**
   * Alerts shown on the desktop.
   *
   * @return string
   *
   */
  public function renderAlerts(): string
  {
    /** @var Counter */
    $counter = $this->getService(Counter::class);
    $unread = $counter->unreadMessages();
    $withoutFollowup = $counter->messagesWithoutFollowup();

    return
      ''
      . ($unread > 0 ? '
        <a href="' . $this->env()->projectUrl . '/linkedin-messages?filters%5BfMessageRead%5D=1">'
          . $unread . ' ' . $this->translate('unread LinkedIn messages')
        . '</a><br/>
      ' : '')
      . ($withoutFollowup > 0 ? '
        <a href="' . $this->env()->projectUrl . '/linkedin-messages?filters%5BfMessageDirection%5D=1&filters%5BfMessageWithPlan%5D=2">'
          . $withoutFollowup . ' ' . $this->translate('LinkedIn messages without planned follow-up')
        . '</a><br/>
      ' : '')
    ;
  }

  /**
   * Renders the second sidebar of the app.
   *
   * @return string
   *
   */
  public function renderSecondSidebar(): string
  {
    /** @var Counter */
    $counter = $this->getService(Counter::class);

    return '
      ' . $this->secondSidebarTitle() . '
      <div class="app-sidebar-buttons">
        ' . $this->secondSidebarButton('linkedin-messages', 'fas fa-envelope', $this->translate('Messages'), $counter->unreadMessages()) . '
        ' . $this->secondSidebarButton('linkedin-messages/followups', 'fas fa-calendar-check', $this->translate('Follow-ups')) . '
        ' . $this->secondSidebarButton('calendar?show=linkedin-messages', 'fas fa-calendar-days', $this->translate('Calendar')) . '
        ' . $this->secondSidebarButton('linkedin-messages/profiles', 'fas fa-id-badge', $this->translate('Profiles')) . '
        ' . $this->secondSidebarButton('linkedin-messages/campaigns', 'fas fa-bullhorn', $this->translate('Campaigns')) . '
        ' . $this->secondSidebarButton('linkedin-messages/replies', 'fas fa-reply', $this->translate('Replies')) . '
        ' . $this->secondSidebarButton('linkedin-messages/accounts', 'fab fa-linkedin', $this->translate('Accounts')) . '
        ' . $this->secondSidebarButton('linkedin-messages/tags', 'fas fa-tags', $this->translate('Tags')) . '
        ' . $this->secondSidebarButton('linkedin-messages/settings', 'fas fa-gear', $this->translate('Settings')) . '
      </div>
    ';
  }

}
