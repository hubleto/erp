<?php

namespace HubletoApp\Community\Customers\Models;

use HubletoApp\Community\Settings\Models\ContactType;

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
      'id_person' => [
        'type' => 'lookup',
        'title' => $this->translate('Person'),
        'model' => Person::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'id_contact_type' => [
        'type' => 'lookup',
        'title' => $this->translate('Contact Category'),
        'model' => \HubletoApp\Community\Settings\Models\ContactType::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'type' => [
        'type' => 'varchar',
        'title' => $this->translate('Type'),
        'enumValues' => ['email' => $this->translate('Email'), 'number' => $this->translate('Phone Number'), 'other' => $this->translate('Other')],
        'required' => true,
      ],
      'value' => [
        'type' => 'varchar',
        'title' => $this->translate('Value'),
        'required' => true,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['title'] = 'Contacts';
    $description['ui']['addButtonText'] = 'Add Company';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }

  public function prepareLoadRecordQuery(array|null $includeRelations = null, int $maxRelationLevel = 0, mixed $query = null, int $level = 0): mixed {
    $query = parent::prepareLoadRecordQuery($includeRelations, 3, $query, $level);
    return $query;
  }
}
