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

class Message extends \HubletoMain\Model
{
  public string $table = 'discussions_messages';
  public string $recordManagerClass = RecordManagers\Message::class;
  public ?string $lookupSqlValue = 'concat("Message #", {%TABLE%}.id)';
  public ?string $lookupUrlDetail = 'messages/{%ID%}';

  public array $relations = [
    'DISCUSSION' => [ self::BELONGS_TO, Discussion::class, 'id_discussion', 'id' ],
    'FROM' => [ self::BELONGS_TO, User::class, 'id_from', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_discussion' => (new Lookup($this, $this->translate('Discussion'), Discussion::class))->setProperty('defaultVisibility', true)->setRequired(),
      'id_from' => (new Lookup($this, $this->translate('From'), User::class))->setProperty('defaultVisibility', true),
      'from_email' => (new Varchar($this, $this->translate('From (Email)')))->setProperty('defaultVisibility', true),
      'message' => (new Text($this, $this->translate('Text')))->setProperty('defaultVisibility', true),
      'sent' => (new Datetime($this, $this->translate('Sent')))->setProperty('defaultVisibility', true),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Message';
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
