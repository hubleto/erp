<?php

namespace Hubleto\App\Community\Dashboards\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Lookup;


class Dashboard extends \Hubleto\Erp\Model
{
  public string $table = 'dashboards';
  public string $recordManagerClass = RecordManagers\Dashboard::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public array $relations = [
    'OWNER' => [ self::HAS_ONE, User::class, 'id', 'id_owner' ],
    'PANELS' => [ self::HAS_MANY, Panel::class, 'id_dashboard', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_owner' => (new Lookup($this, $this->translate("Owner"), User::class))->setReactComponent('InputUserSelect')->setRequired()->setDefaultVisible(),
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired()->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'slug' => (new Varchar($this, $this->translate('Slug')))->setRequired()->setDefaultVisible(),
      'color' => (new Color($this, $this->translate('Color')))->setRequired()->setDefaultVisible(),
      'is_default' => (new Boolean($this, $this->translate('Is default')))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add dashboard');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

}
