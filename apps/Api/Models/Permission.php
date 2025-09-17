<?php

namespace Hubleto\App\Community\Api\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Image;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Password;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;


class Permission extends \Hubleto\Erp\Model
{
  public string $table = 'api_permissions';
  public string $recordManagerClass = RecordManagers\Permission::class;
  public ?string $lookupSqlValue = 'concat("Api permission #", {%TABLE%}.id)';
  public ?string $lookupUrlDetail = 'api/permissions/{%ID%}';

  public array $relations = [
    'KEY' => [ self::BELONGS_TO, Key::class, 'id_key', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_key' => (new Lookup($this, $this->translate('Key'), Key::class))->setProperty('defaultVisibility', true)->setRequired(),
      'app' => (new Varchar($this, $this->translate('App')))->setProperty('defaultVisibility', true)->setRequired()->setDescription('Namespace of the app, e.g. `Hubleto\App\Community\Deals`.'),
      'controller' => (new Varchar($this, $this->translate('Controller')))->setProperty('defaultVisibility', true)->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Permission';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
