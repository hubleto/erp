<?php

namespace HubletoApp\Community\Contacts\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;

class Value extends \Hubleto\Framework\Models\Model
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

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = $this->translate('Contacts');
    $description->ui['addButtonText'] = $this->translate('Add Customer');
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

  public function getTypeFromValue(string $value): string
  {
    $type = 'other';
    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
      $type = 'email';
    } elseif (str_starts_with(strtolower($value), 'http')) {
      $type = 'url';
    } elseif (preg_match('/^[\+|0-9| ]+$/', $value)) {
      $type = 'number';
    }
    return $type;
  }

  public function onBeforeCreate(array $record): array
  {
    $record['type'] = $this->getTypeFromValue($record['value']);
    return $record;
  }

  public function onBeforeUpdate(array $record): array
  {
    $record['type'] = $this->getTypeFromValue($record['value']);
    return $record;
  }

}
