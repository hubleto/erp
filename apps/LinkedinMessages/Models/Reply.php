<?php

namespace Hubleto\App\Community\LinkedinMessages\Models;

use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\App\Community\Auth\Models\User;

class Reply extends \Hubleto\Erp\Model
{
  public const STATUS_SENT = 1;
  public const STATUS_MANUAL = 2;
  public const STATUS_FAILED = 3;
  public const STATUS_MARKED_SENT = 4;

  public string $table = 'linkedin_replies';
  public string $recordManagerClass = RecordManagers\Reply::class;
  public ?string $lookupSqlValue = 'left(`{%TABLE%}`.body, 60)';
  public ?string $lookupUrlDetail = 'linkedin-messages/replies/{%ID%}';

  public array $relations = [
    'MESSAGE' => [ self::BELONGS_TO, Message::class, 'id_message', 'id' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_message' => (new Lookup($this, $this->translate('Message'), Message::class))->setRequired()->setDefaultVisible()->setReadonly(),
      'id_owner' => (new Lookup($this, $this->translate('Sent by'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->authProvider()->getUserId()),
      'body' => (new Text($this, $this->translate('Reply')))->setRequired()->setDefaultVisible(),
      'status' => (new Integer($this, $this->translate('Status')))->setDefaultVisible()
        ->setEnumValues([
          self::STATUS_SENT => $this->translate('Sent via LinkedIn API'),
          self::STATUS_MANUAL => $this->translate('Waiting for manual sending'),
          self::STATUS_FAILED => $this->translate('Sending failed'),
          self::STATUS_MARKED_SENT => $this->translate('Sent manually'),
        ])
        ->setEnumCssClasses([
          self::STATUS_SENT => 'badge-success',
          self::STATUS_MANUAL => 'badge-warning',
          self::STATUS_FAILED => 'badge-danger',
          self::STATUS_MARKED_SENT => 'badge-success',
        ])
        ->setDefaultValue(self::STATUS_MANUAL),
      'error_info' => (new Text($this, $this->translate('Details')))->setReadonly(),
      'sent_at' => (new DateTime($this, $this->translate('Sent at')))->setDefaultVisible()->setReadonly(),
      'date_created' => (new DateTime($this, $this->translate('Created')))->setReadonly()->setDefaultValue(date('Y-m-d H:i:s')),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    $description->ui['filters'] = [
      'fReplyStatus' => [
        'title' => $this->translate('Status'),
        'options' => [
          0 => $this->translate('All'),
          self::STATUS_MANUAL => $this->translate('Waiting for manual sending'),
          self::STATUS_FAILED => $this->translate('Sending failed'),
          self::STATUS_SENT => $this->translate('Sent via LinkedIn API'),
          self::STATUS_MARKED_SENT => $this->translate('Sent manually'),
        ],
      ],
    ];
    $description->ui['addButtonText'] = $this->translate('Add reply');
    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->ui['addButtonText'] = $this->translate('Add reply');
    return $description;
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
  }

  public function getMaxReadLevelForLoadFormData(): int
  {
    return 1;
  }

  public function onBeforeCreate(array $record): array
  {
    $record = parent::onBeforeCreate($record);
    if (empty($record['id_owner'])) {
      $record['id_owner'] = $this->authProvider()->getUserId();
    }
    return $record;
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    // user confirmed that the reply was sent manually on LinkedIn
    if (
      ($originalRecord['status'] ?? 0) != self::STATUS_MARKED_SENT
      && ($savedRecord['status'] ?? 0) == self::STATUS_MARKED_SENT
      && empty($savedRecord['sent_at'])
    ) {
      $this->record->where('id', $savedRecord['id'])->update(['sent_at' => date('Y-m-d H:i:s')]);
    }

    return $savedRecord;
  }
}
