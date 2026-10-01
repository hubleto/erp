<?php

namespace Hubleto\App\Community\LinkedinMessages\Models;

use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\App\Community\Auth\Models\User;

class Account extends \Hubleto\Erp\Model
{
  public const STATUS_CONNECTED = 1;
  public const STATUS_EXPIRED = 2;
  public const STATUS_ERROR = 3;

  public string $table = 'linkedin_accounts';
  public string $recordManagerClass = RecordManagers\Account::class;
  public ?string $lookupSqlValue = '`{%TABLE%}`.name';
  public ?string $lookupUrlDetail = 'linkedin-messages/accounts/{%ID%}';

  public array $relations = [
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ],
    'MESSAGES' => [ self::HAS_MANY, Message::class, 'id_account', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->authProvider()->getUserId()),
      'linkedin_member_id' => (new Varchar($this, $this->translate('LinkedIn member ID')))->setReadonly(),
      'name' => (new Varchar($this, $this->translate('Name')))->setCssClass('font-bold')->setDefaultVisible()->setReadonly(),
      'headline' => (new Varchar($this, $this->translate('Headline')))->setDefaultVisible()->setReadonly(),
      'email' => (new Varchar($this, $this->translate('Email')))->setIcon(self::COLUMN_EMAIL_DEFAULT_ICON)->setDefaultVisible()->setReadonly(),
      'profile_url' => (new Varchar($this, $this->translate('Profile URL')))->setReactComponent('InputHyperlink')->setReadonly(),
      'picture_url' => (new Varchar($this, $this->translate('Picture URL')))->setReadonly(),
      'locale' => (new Varchar($this, $this->translate('Locale')))->setReadonly(),
      'access_token' => (new Text($this, $this->translate('Access token')))->setHidden(),
      'refresh_token' => (new Text($this, $this->translate('Refresh token')))->setHidden(),
      'token_expires_at' => (new DateTime($this, $this->translate('Token expires at')))->setDefaultVisible()->setReadonly(),
      'scopes' => (new Varchar($this, $this->translate('Granted scopes')))->setReadonly(),
      'status' => (new Integer($this, $this->translate('Status')))->setDefaultVisible()->setReadonly()
        ->setEnumValues([
          self::STATUS_CONNECTED => $this->translate('Connected'),
          self::STATUS_EXPIRED => $this->translate('Token expired'),
          self::STATUS_ERROR => $this->translate('Error'),
        ])
        ->setEnumCssClasses([
          self::STATUS_CONNECTED => 'badge-success',
          self::STATUS_EXPIRED => 'badge-warning',
          self::STATUS_ERROR => 'badge-danger',
        ]),
      'last_sync_at' => (new DateTime($this, $this->translate('Last synchronization')))->setDefaultVisible()->setReadonly(),
      'last_sync_info' => (new Text($this, $this->translate('Last synchronization result')))->setReadonly(),
      'changelog_cursor' => (new Varchar($this, $this->translate('Changelog cursor')))->setReadonly(),
      'date_created' => (new DateTime($this, $this->translate('Created')))->setReadonly()->setDefaultValue(date('Y-m-d H:i:s')),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    $description->ui['addButtonText'] = $this->translate('Add account');
    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->ui['addButtonText'] = $this->translate('Add account');
    return $description;
  }

  public function getLookupDetails(array $dataRaw): string
  {
    return (string) ($dataRaw['headline'] ?? '');
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
    if (empty($record['status'])) {
      $record['status'] = self::STATUS_ERROR;
    }
    return $record;
  }

  public function onBeforeDelete(int $id): int
  {
    // messages keep their history; detach them from the account that is being removed
    $mMessage = $this->getModel(Message::class);
    $mMessage->record->where('id_account', $id)->update(['id_account' => null]);
    return parent::onBeforeDelete($id);
  }
}
