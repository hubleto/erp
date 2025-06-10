<?php

namespace HubletoApp\Community\Contacts\Models;

use ADIOS\Core\Db\Column\Lookup;
use ADIOS\Core\Db\Column\Varchar;

class Value extends \HubletoMain\Core\Models\Model
{
  public string $table = 'contact_values';
  public string $recordManagerClass = RecordManagers\Value::class;
  public ?string $lookupSqlValue = '{%TABLE%}.value';

  public array $relations = [
    'CONTACT' => [ self::BELONGS_TO, Contact::class, 'id_contact', 'id' ],
    'CATEGORY' => [ self::HAS_ONE, Category::class, 'id_category', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class, "CASCADE"))->setRequired(),
      'id_category' => (new Lookup($this, $this->translate('Category'), Category::class)),
      'type' => (new Varchar($this, $this->translate('Type')))
        ->setEnumValues([
          'email' => $this->translate('Email'),
          'number' => $this->translate('Number'),
          'url' => $this->translate('URL'),
          'other' => $this->translate('Other')
        ])
        ->setRequired()
      ,
      'value' => (new Varchar($this, $this->translate('Value')))->setRequired(),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Contacts';
    $description->ui['addButtonText'] = 'Add Customer';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    if ($this->main->urlParamAsInteger('idContact') != 0) {
      // $description->permissions = [
      //   'canRead' => $this->main->permissions->granted($this->fullName . ':Read'),
      //   'canCreate' => $this->main->permissions->granted($this->fullName . ':Create'),
      //   'canUpdate' => $this->main->permissions->granted($this->fullName . ':Update'),
      //   'canDelete' => $this->main->permissions->granted($this->fullName . ':Delete'),
      // ];
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }
}
