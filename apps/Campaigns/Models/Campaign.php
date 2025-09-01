<?php

namespace Hubleto\App\Community\Campaigns\Models;

use Hubleto\App\Community\Campaigns\Lib;

use Hubleto\App\Community\Settings\Models\User;
use Hubleto\App\Community\Mail\Models\Mail;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\App\Community\Pipeline\Models\Pipeline;
use Hubleto\App\Community\Pipeline\Models\PipelineStep;

use Hubleto\App\Community\Leads\Models\LeadCampaign;

class Campaign extends \Hubleto\Erp\Model
{
  public string $table = 'campaigns';
  public string $recordManagerClass = RecordManagers\Campaign::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id'],
    'PIPELINE' => [ self::HAS_ONE, Pipeline::class, 'id', 'id_pipeline'],
    'PIPELINE_STEP' => [ self::HAS_ONE, PipelineStep::class, 'id', 'id_pipeline_step'],

    'CONTACTS' => [ self::HAS_MANY, CampaignContact::class, 'id_deal', 'id'],
    'TASKS' => [ self::HAS_MANY, CampaignTask::class, 'id_deal', 'id'],
  ];

  /**
   * [Description for describeColumns]
   *
   * @return array
   * 
   */
  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setProperty('defaultVisibility', true)->setCssClass('font-bold'),
      'utm_source' => (new Varchar($this, $this->translate('UTM source')))->setProperty('defaultVisibility', true),
      'utm_campaign' => (new Varchar($this, $this->translate('UTM campaign')))->setProperty('defaultVisibility', true),
      'utm_term' => (new Varchar($this, $this->translate('UTM term')))->setProperty('defaultVisibility', true),
      'utm_content' => (new Varchar($this, $this->translate('UTM content')))->setProperty('defaultVisibility', true),
      'target_audience' => (new Text($this, $this->translate('Target audience')))->setProperty('defaultVisibility', true),
      'goal' => (new Text($this, $this->translate('Goal')))->setProperty('defaultVisibility', true),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'mail_body' => (new Text($this, $this->translate('Mail body (HTML)')))->setReactComponent('InputWysiwyg'),
      'color' => (new Color($this, $this->translate('Color'))),
      'id_mail_template' => (new Lookup($this, $this->translate('Mail template'), Mail::class))->setProperty('defaultVisibility', true),
      'id_pipeline' => (new Lookup($this, $this->translate('Pipeline'), Pipeline::class)),
      'id_pipeline_step' => (new Lookup($this, $this->translate('Pipeline step'), PipelineStep::class))->setProperty('defaultVisibility', true),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setReactComponent('InputUserSelect')->setProperty('defaultVisibility', true)->setDefaultValue($this->getAuthProvider()->getUserId())->setProperty('defaultVisibility', true),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setProperty('defaultVisibility', true),
      'datetime_created' => (new DateTime($this, $this->translate('Created')))->setProperty('defaultVisibility', true)->setRequired()->setDefaultValue(date('Y-m-d H:i:s')),
    ]);
  }

  /**
   * [Description for describeTable]
   *
   * @return \Hubleto\Framework\Description\Table
   * 
   */
  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add Campaign');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    $description->ui['filters'] = [
      'fCampaignPipelineStep' => Pipeline::buildTableFilterForPipelineSteps($this, 'Phase'),
      'fCampaignClosed' => [
        'title' => $this->translate('Open / Closed'),
        'options' => [
          0 => $this->translate('Open'),
          1 => $this->translate('Closed'),
          2 => $this->translate('All'),
        ],
        'default' => 0,
      ],
    ];


    return $description;
  }

  /**
   * [Description for onAfterCreate]
   *
   * @param array $savedRecord
   * 
   * @return array
   * 
   */
  public function onAfterCreate(array $savedRecord): array
  {

    $mPipeline = $this->getService(Pipeline::class);
    list($defaultPipeline, $idPipeline, $idPipelineStep) = $mPipeline->getDefaultPipelineInGroup('campaigns');
    $savedRecord['id_pipeline'] = $idPipeline;
    $savedRecord['id_pipeline_step'] = $idPipelineStep;

    $this->record->recordUpdate($savedRecord);

    return parent::onAfterCreate($savedRecord);
  }

  /**
   * [Description for onAfterUpdate]
   *
   * @param array $originalRecord
   * @param array $savedRecord
   * 
   * @return array
   * 
   */
  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    $mMail = $this->getService(Mail::class);
    $template = $mMail->record->find((int) ($savedRecord['id_mail_template'] ?? 0));

    if ($template) {
      $bodyHtml = Lib::addUtmVariablesToEmailLinks(
        (string) $template->body_html,
        (string) $savedRecord['utm_source'],
        (string) $savedRecord['utm_campaign'],
        (string) $savedRecord['utm_term'],
        (string) $savedRecord['utm_content'],
      );

      $mCampaign = $this->getService(Campaign::class);
      $mCampaign->record->find((int) $savedRecord['id'])->update([
        'mail_body' => $bodyHtml
      ]);
    }

    return $savedRecord;
  }

}
