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
use Hubleto\App\Community\Settings\Models\User;

class Usage extends \Hubleto\Erp\Model
{
  public string $table = 'api_usages';
  public string $recordManagerClass = RecordManagers\Usage::class;
  public ?string $lookupSqlValue = 'concat("Api usage #", {%TABLE%}.id)';
  public ?string $lookupUrlDetail = 'api/usages/{%ID%}';

  public array $relations = [
    'KEY' => [ self::BELONGS_TO, Key::class, 'id_key', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_key' => (new Lookup($this, $this->translate('Key'), Key::class))->setProperty('defaultVisibility', true)->setRequired(),
      'controller' => (new Varchar($this, $this->translate('Controller')))->setProperty('defaultVisibility', true)->setRequired(),
      'used_on' => (new DateTime($this, $this->translate('Used on')))->setReadonly()->setDefaultValue(date("Y-m-d H:i:s")),
      'ip_address' => (new Varchar($this, $this->translate('IP address')))->setProperty('defaultVisibility', true),
      'status' => (new Varchar($this, $this->translate('Status')))
        ->setEnumValues([
          0 => $this->translate('Success'),
          1 => $this->translate('Error'),
        ])
      ,
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Usage';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
