<?php

namespace Hubleto\App\Community\Projects\Models;

use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Auth\Models\User;

class ProjectRate extends \Hubleto\Erp\Model
{
  public string $table = 'projects_rates';
  public string $recordManagerClass = RecordManagers\ProjectRate::class;

  public array $relations = [
    'PROJECT' => [ self::BELONGS_TO, Project::class, 'id_project', 'id' ],
    'USER' => [ self::BELONGS_TO, User::class, 'id_task', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_project' => (new Lookup($this, $this->translate('Project'), Project::class))->setRequired()->setDefaultVisible(),
      'id_user' => (new Lookup($this, $this->translate('User'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible(),
      'rate' => (new Decimal($this, $this->translate('Rate')))->setUnit('€/hour')->setDecimals(2)->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Assign rate to project');
    $description->show(['header', 'columnSearch', 'fulltextSearch']);
    $description->hide(['footer']);
    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    return $description;
  }

}
