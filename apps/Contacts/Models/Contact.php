<?php

namespace HubletoApp\Community\Contacts\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use HubletoApp\Community\Customers\Models\Customer;
use Hubleto\Framework\Helper;

class Contact extends \Hubleto\Framework\Models\Model
{
  public bool $isExtendableByCustomColumns = true;

  public string $table = 'contacts';
  public string $recordManagerClass = RecordManagers\Contact::class;
  public ?string $lookupSqlValue = "
    concat(
      ifnull({%TABLE%}.first_name, ''), ' ', ifnull({%TABLE%}.last_name, ''),
      ' (', ifnull((select group_concat(value separator ', ') from contact_values where id_contact = {%TABLE%}.id), '- no contact information -'), ')'
    )
  ";
  public ?string $lookupUrlDetail = 'contacts/{%ID%}';

  public array $relations = [
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, 'id_customer' ],
    'VALUES' => [ self::HAS_MANY, Value::class, 'id_contact', 'id' ],
    'TAGS' => [ self::HAS_MANY, ContactTag::class, 'id_contact', 'id' ],
  ];

  // public function translate(string $string, array $vars = []): string
  // {
  //   return parent::translate($string, $vars);
  // }

  public function describeColumns(): array
  {
    return array_merge([
      'salutation' => (new Varchar($this, $this->translate('Salutation'))),
      'title_before' => (new Varchar($this, $this->translate('Title before'))),
      'first_name' => (new Varchar($this, $this->translate('First name')))->setProperty('defaultVisibility', true),
      'middle_name' => (new Varchar($this, $this->translate('Middle name'))),
      'last_name' => (new Varchar($this, $this->translate('Last name')))->setProperty('defaultVisibility', true),
      'title_after' => (new Varchar($this, $this->translate('Title after'))),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setProperty('defaultVisibility', true),
      'is_primary' => (new Boolean($this, $this->translate('Primary Contact')))->setDefaultValue(0),
      'note' => (new Text($this, $this->translate('Notes')))->setProperty('defaultVisibility', true),
      'date_created' => (new Date($this, $this->translate('Date Created')))->setReadonly()->setRequired()->setDefaultValue(date("Y-m-d")),
      'is_valid' => (new Boolean($this, $this->translate('Valid')))->setDefaultValue(1)->setProperty('defaultVisibility', true),
    ], parent::describeColumns());
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = ''; // $this->translate('Contacts');
    $description->ui['addButtonText'] = $this->translate('Add contact');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    $description->columns['virt_email'] = ["title" => $this->translate("Emails")];
    $description->columns['virt_number'] = ["title" => $this->translate("Phone Numbers")];
    $description->columns['virt_tags'] = ["title" => $this->translate("Tags")];

    unset($description->columns['note']);
    unset($description->columns['is_primary']);


    if ($this->main->urlParamAsInteger('idCustomer') > 0) {
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();

    $description->inputs['salutation']->setPredefinedValues([
      $this->translate('Mr.'),
      $this->translate('Mrs.'),
    ]);
    return $description;
  }

  public function onBeforeCreate(array $record): array
  {
    $record['date_created'] = date('Y-m-d');
    return $record;
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    if (isset($savedRecord["TAGS"])) {
      $helper->deleteTags(
        array_column($savedRecord["TAGS"], "id"),
        "HubletoApp/Community/Contacts/Models/ContactTag",
        "id_contact",
        $savedRecord["id"]
      );
    }

    return $savedRecord;
  }

}
