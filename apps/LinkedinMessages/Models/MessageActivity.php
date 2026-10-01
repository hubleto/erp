<?php

namespace Hubleto\App\Custom\LinkedinMessages\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Settings\Models\ActivityType;

class MessageActivity extends \Hubleto\App\Community\Calendar\Models\Activity
{
  public string $table = 'linkedin_message_activities';
  public string $recordManagerClass = RecordManagers\MessageActivity::class;
  public ?string $lookupSqlValue = '`{%TABLE%}`.subject';
  public ?string $lookupUrlDetail = 'linkedin-messages/followups/{%ID%}';

  public array $relations = [
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ],
    'ACTIVITY_TYPE' => [ self::BELONGS_TO, ActivityType::class, 'id_activity_type', 'id' ],
    'MESSAGE' => [ self::BELONGS_TO, Message::class, 'id_message', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_message' => (new Lookup($this, $this->translate('Message'), Message::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    $description->ui['filters'] = [
      'fFollowupCompleted' => [
        'direction' => 'horizontal',
        'options' => [
          1 => $this->translate('Planned'),
          2 => $this->translate('Completed'),
          3 => $this->translate('All'),
        ],
        'default' => 1,
      ],
    ];
    $description->ui['addButtonText'] = $this->translate('Add follow-up');
    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->ui['addButtonText'] = $this->translate('Add follow-up');
    return $description;
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 2;
  }

  public function getMaxReadLevelForLoadFormData(): int
  {
    return 2;
  }
}
