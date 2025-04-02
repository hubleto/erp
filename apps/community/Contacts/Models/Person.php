<?php

namespace HubletoApp\Community\Contacts\Models;

use Illuminate\Database\Eloquent\Builder;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Text;
use \ADIOS\Core\Db\Column\Boolean;
use \ADIOS\Core\Db\Column\Date;

use HubletoApp\Community\Customers\Models\Customer;

class Person extends \HubletoMain\Core\Model
{
  public string $table = 'persons';
  public string $eloquentClass = Eloquent\Person::class;
  public ?string $lookupSqlValue = "concat({%TABLE%}.first_name, ' ', {%TABLE%}.last_name)";

  public array $relations = [
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, 'id_customer' ],
    'CONTACTS' => [ self::HAS_MANY, Contact::class, 'id_person', 'id' ],
    'TAGS' => [ self::HAS_MANY, PersonTag::class, 'id_person', 'id' ],
  ];

  public function translate(string $string, array $vars = []): string
  {
    return parent::translate($string, $vars);
  }

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'first_name' => (new Varchar($this, $this->translate('First name')))->setRequired(),
      'last_name' => (new Varchar($this, $this->translate('Last name')))->setRequired(),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class, 'CASCADE')),
      'note' => (new Text($this, $this->translate('Notes'))),
      'date_created' => (new Date($this, $this->translate('Date Created')))->setReadonly()->setRequired(),
      'is_active' => (new Boolean($this, $this->translate('Active'))),
      'note' => (new Text($this, $this->translate('Notes'))),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = $this->translate('Contacts');
    $description->ui['addButtonText'] = $this->translate('Add Contact');
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;

    $description->columns['virt_email'] = ["title" => $this->translate("Emails")];
    $description->columns['virt_number'] = ["title" => $this->translate("Phone Numbers")];

    //nadstavit aby boli tieto stĺpce posledné
    $tempColumn = $description->columns['date_created'];
    unset($description->columns['date_created']);
    $description->columns['date_created'] = $tempColumn;
    $tempColumn = $description->columns['is_active'];
    unset($description->columns['is_active']);
    $description->columns['is_active'] = $tempColumn;

    unset($description->columns['note']);

    if ($this->main->urlParamAsInteger('idCustomer') > 0) {
      $description->permissions = [
        'canRead' => $this->main->permissions->granted($this->fullName . ':Read'),
        'canCreate' => $this->main->permissions->granted($this->fullName . ':Create'),
        'canUpdate' => $this->main->permissions->granted($this->fullName . ':Update'),
        'canDelete' => $this->main->permissions->granted($this->fullName . ':Delete'),
      ];
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }

  public function describeForm(): \ADIOS\Core\Description\Form
  {
    $description = parent::describeForm();
    $description->defaultValues['is_active'] = 1;
    $description->defaultValues['date_created'] = date("Y-m-d");
    return $description;
  }

  public function prepareLoadRecordQuery(array $includeRelations = [], int $maxRelationLevel = 0, mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareLoadRecordQuery($includeRelations, 1);

    $query = $query->selectRaw("
      (Select value from contacts where id_person = persons.id and type = 'number' LIMIT 1) virt_number,
      (Select value from contacts where id_person = persons.id and type = 'email' LIMIT 1) virt_email
    ");

    return $query;
  }
}
