<?php

namespace Hubleto\App\Community\Dashboards\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Integer;

class Panel extends \Hubleto\Erp\Model
{
  public string $table = 'dashboards_panels';
  public string $recordManagerClass = RecordManagers\Panel::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public array $relations = [
    'DASHBOARD' => [ self::BELONGS_TO, Dashboard::class, 'id_dashboard', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_dashboard' => (new Lookup($this, $this->translate("Dashboard"), Dashboard::class))
        ->setRequired()->setReadonly()->setDefaultValue($this->router()->urlParamAsInteger('idDashboard')),
      'board_url_slug' => (new Varchar($this, $this->translate('Board')))->setRequired(),
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired(),
      'width' => (new Integer($this, $this->translate('Width')))->setDefaultVisible()->setDefaultValue(1)->setEnumValues(
        [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6]
      ),
      'order' => (new Integer($this, $this->translate('Order')))->setDefaultVisible(),
      'configuration' => (new Json($this, $this->translate('Configuration'))),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add panel');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    unset($description->columns['id_dashboard']);

    return $description;
  }

  public function describeInput(string $columnName): \Hubleto\Framework\Description\Input
  {
    $description = parent::describeInput($columnName);
    switch ($columnName) {
      case 'board_url_slug':
        $boards = $this->getService(\Hubleto\App\Community\Dashboards\Manager::class);
        $enumValues = [
          '' => $this->translate('-- Select board to be displayed in panel --'),
        ];
        foreach ($boards->getBoards() as $board) {
          $enumValues[$board['boardUrlSlug']] = $board['app']->manifest['name'] . ': ' . $board['title'];
        }
        $description->setEnumValues($enumValues);
        break;
    }
    return $description;
  }

}
