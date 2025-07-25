<?php

namespace HubletoApp\Community\Discussions\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Image;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Password;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use HubletoApp\Community\Settings\Models\User;

class Member extends \Hubleto\Framework\Models\Model
{
  public string $table = 'discussions_members';
  public string $recordManagerClass = RecordManagers\Member::class;
  public ?string $lookupSqlValue = 'concat("Member #", {%TABLE%}.id)';
  public ?string $lookupUrlDetail = 'members/{%ID%}';

  public array $relations = [
    'DISCUSSION' => [ self::BELONGS_TO, Discussion::class, 'id_discussion', 'id' ],
    'MEMBER' => [ self::BELONGS_TO, User::class, 'id_member', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_discussion' => (new Lookup($this, $this->translate('Discussion'), Discussion::class))->setProperty('defaultVisibility', true)->setRequired(),
      'id_member' => (new Lookup($this, $this->translate('Member'), User::class))->setProperty('defaultVisibility', true)->setRequired()->setDefaultValue($this->main->auth->getUserId()),
      'permissions' => (new Json($this, $this->translate('Permissions')))->setProperty('defaultVisibility', true),

      // 'date_example' => (new Date($this, $this->translate('Date')))->setProperty('defaultVisibility', true)->setReadonly()->setRequired()
      //   ->setDefaultValue(date("Y-m-d"))
      // ,
      // 'datetime_example' => (new DateTime($this, $this->translate('DateTime')))->setProperty('defaultVisibility', true)->setReadonly()->setRequired()
      //   ->setDefaultValue(date("Y-m-d H:i:s"))
      // ,
      // 'integer_example' => (new Integer($this, $this->translate('Integer')))->setProperty('defaultVisibility', true)->setReadonly()->setRequired()
      //   ->setEnumValues(self::INTEGER_ENUM_VALUES)
      //   ->setEnumCssClasses([
      //     self::ENUM_ONE => 'bg-blue-50',
      //     self::ENUM_TWO => 'bg-yellow-50',
      //     self::ENUM_THREE => 'bg-green-50',
      //   ])
      //   ->setDefaultValue(self::ENUM_ONE)
      // ,
      // 'color_example' => (new Color($this, $this->translate('Color')))->setProperty('defaultVisibility', true)->setReadonly()->setRequired(),
      // 'image_example' => (new Image($this, $this->translate('Image')))->setProperty('defaultVisibility', true)->setReadonly()->setRequired(),
      // 'file_example' => (new File($this, $this->translate('File')))->setProperty('defaultVisibility', true)->setReadonly()->setRequired(),
      // 'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setProperty('defaultVisibility', true)->setReadonly()->setRequired()
      //   ->setDefaultValue($this->main->auth->getUserId())
      // ,
      // 'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setProperty('defaultVisibility', true)->setReadonly()->setRequired()
      //   ->setDefaultValue($this->main->auth->getUserId())
      // ,
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Member';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    // Uncomment and modify these lines if you want to define defaultFilter for your model
    // $description->ui['defaultFilters'] = [
    //   'fArchive' => [ 'title' => 'Archive', 'options' => [ 0 => 'Active', 1 => 'Archived' ] ],
    // ];

    return $description;
  }

  // public function describeForm(): \Hubleto\Framework\Description\Form
  // {
  //   return parent::describeForm();
  // }

  // public function onBeforeCreate(array $record): array
  // {
  //   return parent::onBeforeCreate($record);
  // }

  // public function onBeforeUpdate(array $record): array
  // {
  //   return parent::onBeforeUpdate($record);
  // }

  // public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  // {
  //   return parent::onAfterUpdate($originalRecord, $savedRecord);
  // }

  // public function onAfterCreate(array $savedRecord): array
  // {
  //   return parent::onAfterCreate($savedRecord);
  // }

}
