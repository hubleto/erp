<?php

namespace Hubleto\App\Community\Discussions\Models;


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


class Member extends \Hubleto\Erp\Model
{
  public string $table = 'discussions_members';
  public string $recordManagerClass = RecordManagers\Member::class;
  public ?string $lookupSqlValue = 'concat("Member #", {%TABLE%}.id)';
  public ?string $lookupUrlDetail = 'members/{%ID%}';

  public array $relations = [
    'DISCUSSION' => [ self::BELONGS_TO, Discussion::class, 'id_discussion', 'id' ],
    'MEMBER' => [ self::BELONGS_TO, \Hubleto\Framework\Models\User::class, 'id_member', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_discussion' => (new Lookup($this, $this->translate('Discussion'), Discussion::class))->setProperty('defaultVisibility', true)->setRequired(),
      'id_member' => (new Lookup($this, $this->translate('Member'), \Hubleto\Framework\Models\User::class))->setReactComponent('InputUserSelect')->setProperty('defaultVisibility', true)->setRequired()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
      'permissions' => (new Json($this, $this->translate('Permissions')))->setProperty('defaultVisibility', true),
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

    // Uncomment and modify these lines if you want to define table filter for your model
    // $description->ui['filters'] = [
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
