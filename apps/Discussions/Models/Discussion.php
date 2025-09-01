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
use Hubleto\Framework\Db\Column\Virtual;
use Hubleto\App\Community\Projects\Models\Project;
use Hubleto\App\Community\Settings\Models\User;
use Hubleto\App\Community\Pipeline\Models\Pipeline;
use Hubleto\App\Community\Pipeline\Models\PipelineStep;

class Discussion extends \Hubleto\Erp\Model
{
  public string $table = 'discussions';
  public string $recordManagerClass = RecordManagers\Discussion::class;
  public ?string $lookupSqlValue = '{%TABLE%}.topic';
  public ?string $lookupUrlDetail = 'discussions/{%ID%}';

  public array $relations = [
    'MAIN_MOD' => [ self::BELONGS_TO, User::class, 'id_main_mod', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'topic' => (new Varchar($this, $this->translate('Title')))->setProperty('defaultVisibility', true)->setRequired(),
      'description' => (new Text($this, $this->translate('Description'))),
      'id_main_mod' => (new Lookup($this, $this->translate('Main MOD'), User::class))->setReactComponent('InputUserSelect')->setProperty('defaultVisibility', true)->setRequired()->setDefaultValue($this->getAuthProvider()->getUserId()),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultValue(false),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'date_created' => (new DateTime($this, $this->translate('Created')))->setReadonly()->setDefaultValue(date("Y-m-d H:i:s")),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Discussion';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    return parent::describeForm();
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
