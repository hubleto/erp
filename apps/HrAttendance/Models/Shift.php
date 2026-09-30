<?php

namespace Hubleto\App\Community\HrAttendance\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;

class Shift extends \Hubleto\Erp\Model
{
  public string $table = 'hr_shifts';
  public string $recordManagerClass = RecordManagers\Shift::class;
  public array $relations = [
    'USER' => [self::BELONGS_TO, User::class, 'id_user', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_user' => (new Lookup($this, $this->translate('Employee'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setRequired(),
      'date_start' => (new DateTime($this, $this->translate('Shift start')))->setDefaultVisible()->setRequired(),
      'date_end' => (new DateTime($this, $this->translate('Shift end')))->setDefaultVisible()->setRequired(),
      'location' => (new Varchar($this, $this->translate('Work location'))),
      'status' => (new Varchar($this, $this->translate('Status')))->setDefaultVisible()->setRequired(),
    ]);
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->inputs['status']->setPredefinedValues([
      $this->translate('Planned'),
      $this->translate('Completed'),
      $this->translate('Cancelled'),
    ]);
    return $description;
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Schedule shift');
    $description->ui['orderBy'] = 'date_start asc';
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['USER'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
  }
}