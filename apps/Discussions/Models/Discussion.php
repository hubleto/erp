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
use Hubleto\Framework\Db\Column\Virtual;
use HubletoApp\Community\Projects\Models\Project;
use HubletoApp\Community\Settings\Models\User;
use HubletoApp\Community\Pipeline\Models\Pipeline;
use HubletoApp\Community\Pipeline\Models\PipelineStep;

class Discussion extends \Hubleto\Framework\Models\Model
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
      'id_main_mod' => (new Lookup($this, $this->translate('Main MOD'), User::class))->setProperty('defaultVisibility', true)->setRequired()->setDefaultValue($this->main->auth->getUserId()),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultValue(false),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'date_created' => (new DateTime($this, $this->translate('Created')))->setReadonly()->setDefaultValue(date("Y-m-d H:i:s")),
      'external_model' => (new Varchar($this, $this->translate('External Model')))->setProperty('defaultVisibility', true),
      'external_id' => (new Integer($this, $this->translate('External ID'))),
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

    $discussionsApp = $this->main->apps->community('Discussions');

    if (isset($description->columns['external_model'])) {
      $enumExternalModels = ['' => '-- No external relation --'];
      foreach ($discussionsApp->getRegisteredExternalModels() as $modelClass => $app) {
        $enumExternalModels[$modelClass] = $app->manifest['nameTranslated'];
      }

      $description->columns['external_model']->setEnumValues($enumExternalModels);
    }

    $fExternalModels = [];
    foreach ($discussionsApp->getRegisteredExternalModels() as $modelClass => $app) {
      $fExternalModels[$modelClass] = $app->manifest['name'];
    }
    $description->ui['defaultFilters'] = [
      'fExternalModels' => [ 'title' => 'External models', 'type' => 'multipleSelectButtons', 'options' => $fExternalModels ],
    ];

    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();

    $discussionsApp = $this->main->apps->community('Discussions');

    $enumExternalModels = ['' => '-- No external relation --'];
    foreach ($discussionsApp->getRegisteredExternalModels() as $modelClass => $app) {
      $enumExternalModels[$modelClass] = $app->manifest['nameTranslated'];
    }

    $description->inputs['external_model']->setEnumValues($enumExternalModels);

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
