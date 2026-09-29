<?php

namespace Hubleto\App\Community\HrRecruitment\Models;

use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class Application extends \Hubleto\Erp\Model
{
  public string $table = 'hr_applications';
  public string $recordManagerClass = RecordManagers\Application::class;
  public ?string $lookupSqlValue = 'concat("Application #", {%TABLE%}.id)';
  public array $relations = [
    'JOB_OPENING' => [self::BELONGS_TO, JobOpening::class, 'id_job_opening', 'id'],
    'CANDIDATE' => [self::BELONGS_TO, Candidate::class, 'id_candidate', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_job_opening' => (new Lookup($this, $this->translate('Job opening'), JobOpening::class))->setDefaultVisible()->setRequired(),
      'id_candidate' => (new Lookup($this, $this->translate('Candidate'), Candidate::class))->setDefaultVisible()->setRequired(),
      'stage' => (new Varchar($this, $this->translate('Stage')))->setDefaultVisible()->setRequired(),
      'status' => (new Varchar($this, $this->translate('Status')))->setDefaultVisible()->setRequired(),
      'date_applied' => (new Date($this, $this->translate('Applied on')))->setDefaultVisible()->setRequired(),
      'date_decided' => (new Date($this, $this->translate('Decision date'))),
      'notes' => (new Text($this, $this->translate('Notes'))),
    ]);
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->inputs['stage']->setPredefinedValues([
      $this->translate('Applied'),
      $this->translate('Screening'),
      $this->translate('Interview'),
      $this->translate('Assessment'),
      $this->translate('Offer'),
    ]);
    $description->inputs['status']->setPredefinedValues([
      $this->translate('In progress'),
      $this->translate('Hired'),
      $this->translate('Rejected'),
      $this->translate('Withdrawn'),
    ]);
    return $description;
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = $this->translate('Applications');
    $description->ui['addButtonText'] = $this->translate('Add application');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['JOB_OPENING', 'CANDIDATE'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
  }
}