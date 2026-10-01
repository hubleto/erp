<?php

namespace Hubleto\App\Community\LinkedinMessages\Models;

use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Virtual;
use Hubleto\App\Community\Auth\Models\User;

class Profile extends \Hubleto\Erp\Model
{
  public string $table = 'linkedin_profiles';
  public string $recordManagerClass = RecordManagers\Profile::class;
  public ?string $lookupSqlValue = "trim(concat(ifnull(`{%TABLE%}`.first_name, ''), ' ', ifnull(`{%TABLE%}`.last_name, '')))";
  public ?string $lookupUrlDetail = 'linkedin-messages/profiles/{%ID%}';

  public array $relations = [
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ],
    'MESSAGES' => [ self::HAS_MANY, Message::class, 'id_profile', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->authProvider()->getUserId()),
      'linkedin_urn' => (new Varchar($this, $this->translate('LinkedIn identifier'))),
      'first_name' => (new Varchar($this, $this->translate('First name')))->setCssClass('font-bold')->setDefaultVisible(),
      'last_name' => (new Varchar($this, $this->translate('Last name')))->setCssClass('font-bold')->setDefaultVisible(),
      'headline' => (new Varchar($this, $this->translate('Headline')))->setDefaultVisible(),
      'company' => (new Varchar($this, $this->translate('Company')))->setDefaultVisible(),
      'location' => (new Varchar($this, $this->translate('Location'))),
      'email' => (new Varchar($this, $this->translate('Email')))->setIcon(self::COLUMN_EMAIL_DEFAULT_ICON),
      'profile_url' => (new Varchar($this, $this->translate('Profile URL')))->setReactComponent('InputHyperlink')->setDefaultVisible(),
      'picture_url' => (new Varchar($this, $this->translate('Picture URL'))),
      'note' => (new Text($this, $this->translate('Notes'))),
      'date_created' => (new DateTime($this, $this->translate('Created')))->setReadonly()->setDefaultValue(date('Y-m-d H:i:s')),
      'virt_message_count' => (new Virtual($this, $this->translate('Messages')))->setDefaultVisible()
        ->setProperty('sql', "
          select count(*) from `linkedin_messages` `m` where `m`.`id_profile` = `linkedin_profiles`.`id`
        "),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    $description->ui['addButtonText'] = $this->translate('Add profile');
    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->ui['addButtonText'] = $this->translate('Add profile');
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
    return $record;
  }

  public function onBeforeDelete(int $id): int
  {
    $mMessage = $this->getModel(Message::class);
    $mMessage->record->where('id_profile', $id)->update(['id_profile' => null]);
    return parent::onBeforeDelete($id);
  }
}
