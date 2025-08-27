<?php

namespace Hubleto\App\Community\Projects\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Deals\Models\Deal;

class ProjectDeal extends \Hubleto\Erp\Model
{
  public string $table = 'projects_deals';
  public string $recordManagerClass = RecordManagers\ProjectDeal::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'PROJECT'   => [ self::BELONGS_TO, Project::class, 'id_project', 'id'],
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_project' => (new Lookup($this, $this->translate('Project'), Project::class))->setRequired(),
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Project Deals';
    $description->ui["addButtonText"] = $this->translate("Add deal");

    if ($this->getRouter()->urlParamAsInteger('idProject') > 0) {
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }
}
