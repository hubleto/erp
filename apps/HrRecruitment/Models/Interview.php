<?php

namespace Hubleto\App\Community\HrRecruitment\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class Interview extends \Hubleto\Erp\Model
{
  public string $table = 'hr_interviews';
  public string $recordManagerClass = RecordManagers\Interview::class;
  public array $relations = [
    'APPLICATION' => [self::BELONGS_TO, Application::class, 'id_application', 'id'],
    'INTERVIEWER' => [self::BELONGS_TO, User::class, 'id_interviewer', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_application' => (new Lookup($this, $this->translate('Application'), Application::class))->setDefaultVisible()->setRequired(),
      'id_interviewer' => (new Lookup($this, $this->translate('Interviewer'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setRequired(),
      'date_start' => (new DateTime($this, $this->translate('Start')))->setDefaultVisible()->setRequired(),
      'date_end' => (new DateTime($this, $this->translate('End'))),
      'location' => (new Varchar($this, $this->translate('Location or meeting link'))),
      'status' => (new Varchar($this, $this->translate('Status')))->setDefaultVisible()->setRequired(),
      'feedback' => (new Text($this, $this->translate('Interview feedback'))),
    ]);
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->inputs['status']->setPredefinedValues([
      $this->translate('Scheduled'),
      $this->translate('Completed'),
      $this->translate('Cancelled'),
    ]);
    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['APPLICATION', 'APPLICATION.JOB_OPENING', 'APPLICATION.CANDIDATE', 'INTERVIEWER'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 2;
  }
}