<?php

namespace HubletoApp\Community\Events\Models;

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

class Attendee extends \Hubleto\Framework\Models\Model
{
  public string $table = 'events_attendees';
  public string $recordManagerClass = RecordManagers\Attendee::class;
  public ?string $lookupSqlValue = 'concat(ifnull({%TABLE%}.full_name, ""), " ", ifnull({%TABLE%}.email, ""))';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'salutation' => (new Varchar($this, $this->translate('Salutation')))->setProperty('defaultVisibility', true),
      'title_before' => (new Varchar($this, $this->translate('Title before')))->setProperty('defaultVisibility', true),
      'full_name' => (new Varchar($this, $this->translate('Full name')))->setProperty('defaultVisibility', true),
      'title_after' => (new Varchar($this, $this->translate('Title after')))->setProperty('defaultVisibility', true),
      'email' => (new Varchar($this, $this->translate('Email')))->setProperty('defaultVisibility', true),
      'phone' => (new Varchar($this, $this->translate('Phone')))->setProperty('defaultVisibility', true),
      'social_profile_url_1' => (new Varchar($this, $this->translate('Social profile URL #1'))),
      'social_profile_url_2' => (new Varchar($this, $this->translate('Social profile URL #2'))),
      'social_profile_url_3' => (new Varchar($this, $this->translate('Social profile URL #3'))),
      'social_profile_url_4' => (new Varchar($this, $this->translate('Social profile URL #4'))),
      'social_profile_url_5' => (new Varchar($this, $this->translate('Social profile URL #5'))),
      'notes' => (new Varchar($this, $this->translate('Notes')))->setProperty('defaultVisibility', true),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Attendee';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    // Uncomment and modify these lines if you want to define defaultFilter for your model
    // $description->ui['defaultFilters'] = [
    //   'fArchive' => [ 'title' => 'Archive', 'options' => [ 0 => 'Active', 1 => 'Archived' ] ],
    // ];

    return $description;
  }

  public function onBeforeCreate(array $record): array
  {
    return parent::onBeforeCreate($record);
  }

  public function onBeforeUpdate(array $record): array
  {
    return parent::onBeforeUpdate($record);
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    return parent::onAfterUpdate($originalRecord, $savedRecord);
  }

  public function onAfterCreate(array $savedRecord): array
  {
    return parent::onAfterCreate($savedRecord);
  }

}
