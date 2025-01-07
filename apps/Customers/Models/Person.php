<?php

namespace HubletoApp\Customers\Models;

use Illuminate\Database\Eloquent\Builder;

class Person extends \HubletoCore\Core\Model
{
  public string $table = 'persons';
  public string $eloquentClass = Eloquent\Person::class;
  public ?string $lookupSqlValue = "concat({%TABLE%}.first_name, ' ', {%TABLE%}.last_name)";

  public array $relations = [
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company' ],
    'CONTACTS' => [ self::HAS_MANY, Contact::class, 'id_person', 'id' ],
    //'ADDRESSES' => [ self::HAS_MANY, Address::class, 'id_person', 'id' ],
    'TAGS' => [ self::HAS_MANY, PersonTag::class, 'id_person', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'first_name' => [
        'type' => 'varchar',
        'title' => $this->translate('First name'),
        'required' => true,
      ],
      'last_name' => [
        'type' => 'varchar',
        'title' => $this->translate('Last name'),
        'required' => true,
      ],
      'id_company' => [
        'type' => 'lookup',
        'title' => $this->translate('Company'),
        'model' => Company::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => false,
      ],
      'is_main' => [
        'type' => 'boolean',
        'title' => $this->translate('Main Contact'),
      ],
      'note' => [
        'type' => 'text',
        'title' => $this->translate('Notes'),
        'required' => false,
      ],
      'is_active' => [
        'type' => 'boolean',
        'title' => $this->translate('Active'),
        'required' => false,
        'default' => 1,
      ],
      'date_created' => [
        'type' => 'date',
        'title' => $this->translate('Date Created'),
        'required' => true,
        'readonly' => true,
      ],
    ]));
  }

  //v tablePersons
  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe();
    $description['ui']['title'] = $this->translate('Contact Persons');
    $description['ui']['addButtonText'] = $this->translate('Add Contact Person');
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    $description['columns']['virt_email'] = ["title" => $this->translate("Emails")];
    $description['columns']['virt_number'] = ["title" => $this->translate("Phone Numbers")];
    unset($description['columns']['is_main']);
    unset($description['columns']['note']);

    //nadstavit aby boli tieto stĺpce posledné
    $tempColumn = $description['columns']['date_created'];
    unset($description['columns']['date_created']);
    $description['columns']['date_created'] = $tempColumn;
    $tempColumn = $description['columns']['is_active'];
    unset($description['columns']['is_active']);
    $description['columns']['is_active'] = $tempColumn;

    return $description;
  }

  public function formDescribe(array $description = []): array
  {
    $description = parent::formDescribe();
    $description['defaultValues']['is_active'] = 1;
    $description['defaultValues']['is_main'] = 0;
    $description['includeRelations'] = [
      /* 'ADDRESSES', */
      'CONTACTS',
      'TAGS'
    ];
    return $description;
  }

  public function prepareLoadRecordQuery(array|null $includeRelations = null, int $maxRelationLevel = 0, $query = null, int $level = 0)
  {
    $query = parent::prepareLoadRecordQuery($includeRelations, 1);

    $query = $query->selectRaw("
      (Select value from contacts where id_person = persons.id and type = 'number' LIMIT 1) virt_number,
      (Select value from contacts where id_person = persons.id and type = 'email' LIMIT 1) virt_email
    ");
      //(Select concat(street_line_1,', ', street_line_2, ', ', city) from addresses where id_person = persons.id LIMIT 1) virt_address

    return $query;
  }
}
