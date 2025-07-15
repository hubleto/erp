<?php

namespace HubletoApp\Community\Dashboards\Models;

use ADIOS\Core\Db\Column\Lookup;
use ADIOS\Core\Db\Column\Varchar;
use ADIOS\Core\Db\Column\Json;

class Panel extends \HubletoMain\Core\Models\Model
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
        ->setRequired()->setReadonly()->setDefaultValue($this->main->urlParamAsInteger('idDashboard')),
      'board_url_slug' => (new Varchar($this, $this->translate('Board')))->setRequired(),
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired(),
      'configuration' => (new Json($this, $this->translate('Configuration'))),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add panel');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    unset($description->columns['id_dashboard']);

    return $description;
  }

  public function describeInput(string $columnName): \ADIOS\Core\Description\Input
  {
    $description = parent::describeInput($columnName);
    switch ($columnName) {
      case 'board_url_slug':
        $dashboardsApp = $this->main->apps->community('Dashboards');
        $boards = $dashboardsApp->getBoards();
        $enumValues = [
          '' => $this->translate('-- Select board to be displayed in panel --'),
        ];
        foreach ($boards as $board) {
          $enumValues[$board['boardUrlSlug']] = $board['app']->manifest['name'] . ': ' . $board['title'];
        }
        $description->setEnumValues($enumValues);
      break;
    }
    return $description;
  }

}
