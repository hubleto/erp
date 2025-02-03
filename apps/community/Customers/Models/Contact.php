<?php

namespace HubletoApp\Community\Customers\Models;

use HubletoApp\Community\Settings\Models\ContactType;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Varchar;

class Contact extends \HubletoMain\Core\Model
{
  public string $table = 'contacts';
  public string $eloquentClass = Eloquent\Contact::class;
  public ?string $lookupSqlValue = '{%TABLE%}.value';

  public array $relations = [
    'PERSON' => [ self::BELONGS_TO, Person::class, 'id_person', 'id' ],
    'CONTACT_TYPE' => [ self::HAS_ONE, ContactType::class, 'id_contact_type', 'id'],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_person' => (new Lookup($this, $this->translate('Person'), Person::class))->setRequired(),
      'id_contact_type' => (new Lookup($this, $this->translate('Contact Category'), ContactType::class))->setRequired(),
      'type' => (new Varchar($this, $this->translate('Type')))
        ->setEnumValues(['email' => $this->translate('Email'), 'number' => $this->translate('Phone Number'), 'other' => $this->translate('Other')])
        ->setRequired()
      ,
      'value' => (new Varchar($this, $this->translate('Value')))->setRequired(),
    ]));
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Contacts';
    $description->ui['addButtonText'] = 'Add Company';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;

    if ($this->main->urlParamAsBool('inForm') == true) {
      $description->permissions = [
        'canRead' => $this->main->permissions->granted($this->fullName . ':Read'),
        'canCreate' => $this->main->permissions->granted($this->fullName . ':Create'),
        'canUpdate' => $this->main->permissions->granted($this->fullName . ':Update'),
        'canDelete' => $this->main->permissions->granted($this->fullName . ':Delete'),
      ];
      $description->columns = [];
      $description->ui = [];
    }

    return $description;
  }

  public function prepareLoadRecordQuery(array $includeRelations = [], int $maxRelationLevel = 0, mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareLoadRecordQuery($includeRelations, 3, $query, $level);
    return $query;
  }
}
