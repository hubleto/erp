<?php

namespace Hubleto\App\Custom\LinkedinMessages\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Virtual;
use Hubleto\Framework\Helper;
use Hubleto\App\Community\Auth\Models\User;

class Message extends \Hubleto\Erp\Model
{
  public const DIRECTION_INCOMING = 1;
  public const DIRECTION_OUTGOING = 2;

  public string $table = 'linkedin_messages';
  public string $recordManagerClass = RecordManagers\Message::class;
  public ?string $lookupSqlValue = "coalesce(nullif(`{%TABLE%}`.subject, ''), left(`{%TABLE%}`.body, 60))";
  public ?string $lookupUrlDetail = 'linkedin-messages/{%ID%}';

  public array $relations = [
    'ACCOUNT' => [ self::BELONGS_TO, Account::class, 'id_account', 'id' ],
    'PROFILE' => [ self::BELONGS_TO, Profile::class, 'id_profile', 'id' ],
    'CAMPAIGN' => [ self::BELONGS_TO, Campaign::class, 'id_campaign', 'id' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ],

    'TAGS' => [ self::HAS_MANY, MessageTag::class, 'id_message', 'id' ],
    'ACTIVITIES' => [ self::HAS_MANY, MessageActivity::class, 'id_message', 'id' ],
    'REPLIES' => [ self::HAS_MANY, Reply::class, 'id_message', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_account' => (new Lookup($this, $this->translate('LinkedIn account'), Account::class))->setReadonly(),
      'id_profile' => (new Lookup($this, $this->translate('Sender / counterpart'), Profile::class))->setDefaultVisible()->setIcon(self::COLUMN_CONTACT_DEFAULT_ICON),
      'id_campaign' => (new Lookup($this, $this->translate('Campaign'), Campaign::class))->setDefaultVisible(),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->authProvider()->getUserId()),
      'linkedin_message_id' => (new Varchar($this, $this->translate('LinkedIn message ID')))->setReadonly(),
      'thread_id' => (new Varchar($this, $this->translate('Conversation ID')))->setReadonly(),
      'direction' => (new Integer($this, $this->translate('Direction')))->setDefaultVisible()->setReadonly()
        ->setEnumValues([
          self::DIRECTION_INCOMING => $this->translate('Incoming'),
          self::DIRECTION_OUTGOING => $this->translate('Outgoing'),
        ])
        ->setEnumCssClasses([
          self::DIRECTION_INCOMING => 'bg-blue-50',
          self::DIRECTION_OUTGOING => 'bg-green-50',
        ])
        ->setDefaultValue(self::DIRECTION_INCOMING),
      'subject' => (new Varchar($this, $this->translate('Subject')))->setCssClass('font-bold')->setDefaultVisible(),
      'body' => (new Text($this, $this->translate('Message')))->setDefaultVisible(),
      'sent_at' => (new DateTime($this, $this->translate('Sent / received')))->setDefaultVisible()->setReadonly(),
      'is_read' => (new Boolean($this, $this->translate('Read')))->setDefaultVisible()->setYesText($this->translate('Read'))->setNoText($this->translate('Unread'))->setDefaultValue(0),
      'note' => (new Text($this, $this->translate('Notes'))),
      'date_created' => (new DateTime($this, $this->translate('Imported')))->setReadonly()->setDefaultValue(date('Y-m-d H:i:s')),
      'virt_tags' => (new Virtual($this, $this->translate('Tags')))->setDefaultVisible()
        ->setProperty('sql', "
          SELECT
            GROUP_CONCAT(DISTINCT linkedin_tags.name ORDER BY linkedin_tags.name SEPARATOR ', ')
          FROM `cross_linkedin_message_tags`
          INNER JOIN `linkedin_tags` ON `linkedin_tags`.`id` = `cross_linkedin_message_tags`.`id_tag`
          WHERE `cross_linkedin_message_tags`.`id_message` = `linkedin_messages`.`id`
        "),
      'virt_next_activity_date' => (new Virtual($this, $this->translate('Next follow-up')))->setDefaultVisible()
        ->setProperty('sql', "
          select `a`.`date_start`
          from `linkedin_message_activities` `a`
          where
            `a`.`completed` = 0
            and `a`.`id_message` = `linkedin_messages`.`id`
            and `a`.`date_start` >= date(now())
          order by
            `a`.`date_start` asc
          limit 1
        "),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    $description->ui['filters'] = [
      'fMessageRead' => [
        'direction' => 'horizontal',
        'options' => [
          0 => $this->translate('All'),
          1 => $this->translate('Unread'),
          2 => $this->translate('Read'),
        ],
        'default' => 0,
      ],
      'fMessageDirection' => [
        'title' => $this->translate('Direction'),
        'options' => [
          0 => $this->translate('All'),
          1 => $this->translate('Incoming'),
          2 => $this->translate('Outgoing'),
        ],
      ],
      'fMessageWithPlan' => [
        'title' => $this->translate('Follow-up'),
        'options' => [
          0 => $this->translate('All'),
          1 => $this->translate('With follow-up'),
          2 => $this->translate('Without follow-up'),
        ],
      ],
    ];

    $description->ui['addButtonText'] = $this->translate('Add message');

    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->ui['addButtonText'] = $this->translate('Add message');
    return $description;
  }

  public function getLookupDetails(array $dataRaw): string
  {
    return (string) ($dataRaw['PROFILE']['first_name'] ?? '') . ' ' . (string) ($dataRaw['PROFILE']['last_name'] ?? '');
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['TAGS', 'PROFILE'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 2;
  }

  public function getMaxReadLevelForLoadFormData(): int
  {
    return 2;
  }

  public function onBeforeCreate(array $record): array
  {
    $record = parent::onBeforeCreate($record);
    if (empty($record['id_owner'])) {
      $record['id_owner'] = $this->authProvider()->getUserId();
    }
    if (empty($record['sent_at'])) {
      $record['sent_at'] = date('Y-m-d H:i:s');
    }
    return $record;
  }

  public function onAfterCreate(array $savedRecord): array
  {
    $savedRecord = parent::onAfterCreate($savedRecord);

    // a message created manually by the user is considered handled
    if (($savedRecord['direction'] ?? 0) == self::DIRECTION_OUTGOING && empty($savedRecord['is_read'])) {
      $this->record->where('id', $savedRecord['id'])->update(['is_read' => 1]);
    }

    return $savedRecord;
  }

  public function onBeforeUpdate(array $record): array
  {
    return parent::onBeforeUpdate($record);
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    if (isset($savedRecord['TAGS'])) {
      $helper = $this->getService(Helper::class);
      $helper->deleteTags(
        array_column($savedRecord['TAGS'], 'id'),
        $this->getModel(MessageTag::class),
        'id_message',
        $savedRecord['id']
      );
    }

    return $savedRecord;
  }

  public function onBeforeDelete(int $id): int
  {
    $this->getModel(MessageTag::class)->record->where('id_message', $id)->delete();
    $this->getModel(MessageActivity::class)->record->where('id_message', $id)->delete();
    $this->getModel(Reply::class)->record->where('id_message', $id)->delete();
    return parent::onBeforeDelete($id);
  }
}
