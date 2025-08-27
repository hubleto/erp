<?php

namespace HubletoApp\Community\Dashboards\Models;

use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Settings\Models\User;

class Dashboard extends \HubletoMain\Model
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
      'id_owner' => (new Lookup($this, $this->translate("Owner"), User::class))->setRequired()->setProperty('defaultVisibility', true),
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired()->setProperty('defaultVisibility', true),
      'slug' => (new Varchar($this, $this->translate('Slug')))->setRequired()->setProperty('defaultVisibility', true),
      'color' => (new Color($this, $this->translate('Color')))->setRequired()->setProperty('defaultVisibility', true),
      'is_default' => (new Boolean($this, $this->translate('Is default')))->setProperty('defaultVisibility', true),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add dashboard');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
