<?php

namespace HubletoApp\Community\Customers\Models;

use Illuminate\Database\Eloquent\Builder;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Text;
use \ADIOS\Core\Db\Column\Boolean;
use \ADIOS\Core\Db\Column\Date;

class Person extends \HubletoMain\Core\Model
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
      'first_name' => (new Varchar($this, $this->translate('First name')))->setRequired(),
      'last_name' => (new Varchar($this, $this->translate('Last name')))->setRequired(),
      'id_company' => (new Lookup($this, $this->translate('Company'), Company::class, 'CASCADE')),
      'is_main' => new Boolean($this, $this->translate('Main Contact')),
      'note' => (new Text($this, $this->translate('Notes'))),
      'is_active' => (new Boolean($this, $this->translate('Active')))->setDefaultValue(1),
      'date_created' => (new Date($this, $this->translate('Date Created')))->setReadonly()->setRequired(),
    ]));
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = $this->translate('Contact Persons');
    $description->ui['addButtonText'] = $this->translate('Add Contact Person');
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;
    $description->columns['virt_email'] = ["title" => $this->translate("Emails")];
    $description->columns['virt_number'] = ["title" => $this->translate("Phone Numbers")];
    unset($description->columns['is_main']);
    unset($description->columns['note']);

    //nadstavit aby boli tieto stĺpce posledné
    $tempColumn = $description->columns['date_created'];
    unset($description->columns['date_created']);
    $description->columns['date_created'] = $tempColumn;
    $tempColumn = $description->columns['is_active'];
    unset($description->columns['is_active']);
    $description->columns['is_active'] = $tempColumn;

    if ($this->main->urlParamAsInteger('idCompany') > 0) {
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

  public function describeForm(): \ADIOS\Core\Description\Form
  {
    $description = parent::describeForm();
    $description->defaultValues['is_active'] = 1;
    $description->defaultValues['is_main'] = 0;
    return $description;
  }

  public function prepareLoadRecordQuery(array $includeRelations = [], int $maxRelationLevel = 0, mixed $query = null, int $level = 0): mixed
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
