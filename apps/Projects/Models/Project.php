<?php

namespace HubletoApp\Community\Projects\Models;

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
use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Settings\Models\User;
use HubletoApp\Community\Pipeline\Models\Pipeline;
use HubletoApp\Community\Pipeline\Models\PipelineStep;
use HubletoApp\Community\Contacts\Models\Contact;
use HubletoApp\Community\Customers\Models\Customer;

class Project extends \Hubleto\Framework\Models\Model
{
  public string $table = 'projects';
  public string $recordManagerClass = RecordManagers\Project::class;
  public ?string $lookupSqlValue = 'concat(ifnull({%TABLE%}.identifier, ""), " ", ifnull({%TABLE%}.title, ""))';
  public ?string $lookupUrlDetail = 'projects/{%ID%}';

  public array $relations = [
    'MAIN_DEVELOPER' => [ self::HAS_ONE, User::class, 'id_main_developer', 'id' ],
    'ACCOUNT_MANAGER' => [ self::HAS_ONE, User::class, 'id_account_manager', 'id' ],
    'PHASE' => [ self::HAS_ONE, Phase::class, 'id_phase', 'id' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setProperty('defaultVisibility', false),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class)),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class))->setProperty('defaultVisibility', false),
      'identifier' => (new Varchar($this, $this->translate('Identifier')))->setProperty('defaultVisibility', true)->setRequired()->setCssClass('badge badge-warning text-xl')->setDescription('Leave empty to generate automatically.'),
      'title' => (new Varchar($this, $this->translate('Title')))->setProperty('defaultVisibility', true)->setRequired()->setCssClass('text-2xl text-primary'),
      'description' => (new Text($this, $this->translate('Description'))),
      'id_main_developer' => (new Lookup($this, $this->translate('Main developer'), User::class))->setProperty('defaultVisibility', true)->setRequired()
        ->setDefaultValue($this->main->auth->getUserId())
      ,
      'id_account_manager' => (new Lookup($this, $this->translate('Account manager'), User::class))->setProperty('defaultVisibility', true)->setRequired()
        ->setDefaultValue($this->main->auth->getUserId())
      ,
      'priority' => (new Integer($this, $this->translate('Priority'))),
      'date_start' => (new Date($this, $this->translate('Start')))->setReadonly()->setDefaultValue(date("Y-m-d")),
      'date_deadline' => (new Date($this, $this->translate('Deadline')))->setReadonly()->setDefaultValue(date("Y-m-d")),
      'budget' => (new Integer($this, $this->translate('Budget')))->setProperty('defaultVisibility', true)->setUnit('€'),
      'id_pipeline' => (new Lookup($this, $this->translate('Pipeline'), Pipeline::class))->setDefaultValue(1),
      'id_pipeline_step' => (new Lookup($this, $this->translate('Pipeline step'), PipelineStep::class))->setDefaultValue(null),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultValue(true),
      // 'id_phase' => (new Lookup($this, $this->translate('Phase'), Phase::class))->setProperty('defaultVisibility', true)->setRequired()
      //   ->setDefaultValue($this->main->auth->getUserId())
      // ,
      'color' => (new Color($this, $this->translate('Color')))->setProperty('defaultVisibility', true),
      'online_documentation_folder' => (new Varchar($this, "Online documentation folder"))->setReactComponent('InputHyperlink'),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'date_created' => (new DateTime($this, $this->translate('Created')))->setReadonly()->setDefaultValue(date("Y-m-d H:i:s")),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Project';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    // \$mPhase = $this->main->di->create(Phase::class);
    // $fPhaseOptions = [ ];//0 => 'All' ];
    // foreach ($mPhase->record->orderBy('order', 'asc')->get()?->toArray() as $phase) {
    //   $fPhaseOptions[$phase['id']] = $phase['name'];
    // }

    // $description->ui['defaultFilters'] = [
    //   'fPhase' => [ 'title' => 'Phase', 'type' => 'multipleSelectButtons', 'options' => $fPhaseOptions ],
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

    $mPipeline = $this->main->di->create(Pipeline::class);
    list($defaultPipeline, $idPipeline, $idPipelineStep) = $mPipeline->getDefaultPipelineInfo(Pipeline::TYPE_PROJECT_MANAGEMENT);
    $savedRecord['id_pipeline'] = $idPipeline;
    $savedRecord['id_pipeline_step'] = $idPipelineStep;

    if (empty($savedRecord['identifier'])) {
      $savedRecord["identifier"] = 'P' . $savedRecord["id"];
      $this->record->recordUpdate($savedRecord);
    }

    $this->record->recordUpdate($savedRecord);

    return parent::onAfterCreate($savedRecord);
  }

}
