<?php

namespace Hubleto\App\Community\Contacts\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\Framework\Db\Column\Virtual;
use Hubleto\Framework\Helper;

class Contact extends \Hubleto\Erp\Model
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

  public function describeColumns(): array
  {
    return array_merge([
      'salutation' => (new Varchar($this, $this->translate('Salutation'))),
      'title_before' => (new Varchar($this, $this->translate('Title before')))->setDefaultVisible(),
      'first_name' => (new Varchar($this, $this->translate('First name')))->setDefaultVisible(),
      'middle_name' => (new Varchar($this, $this->translate('Middle name'))),
      'last_name' => (new Varchar($this, $this->translate('Last name')))->setDefaultVisible(),
      'title_after' => (new Varchar($this, $this->translate('Title after'))),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setDefaultVisible()->setIcon(self::COLUMN_ID_CUSTOMER_DEFAULT_ICON),
      'is_primary' => (new Boolean($this, $this->translate('Primary Contact')))->setDefaultValue(0),
      'note' => (new Text($this, $this->translate('Notes'))),
      'date_created' => (new Date($this, $this->translate('Date Created')))->setReadonly()->setRequired()->setDefaultValue(date("Y-m-d")),
      'is_valid' => (new Boolean($this, $this->translate('Valid')))->setDefaultValue(1)->setDefaultVisible(),
      'virt_number' => (new Virtual($this, $this->translate('Phone Numbers')))->setDefaultVisible()
        ->setProperty('sql','
          SELECT
            group_concat(value)
          FROM contact_values
          WHERE contact_values.id_contact = contacts.id
          AND contact_values.type = "number"
        '),
      'virt_email' => (new Virtual($this, $this->translate('Emails')))->setDefaultVisible()
        ->setProperty('sql','
          SELECT
            group_concat(value)
          FROM contact_values
          WHERE contact_values.id_contact = contacts.id
          AND contact_values.type = "email"
        '),
      'virt_tags' => (new Virtual($this, $this->translate('Tags')))->setDefaultVisible()
        ->setProperty('sql',"
          SELECT
            GROUP_CONCAT(DISTINCT contact_tags.name ORDER BY contact_tags.name SEPARATOR ', ')
          FROM `contact_contact_tags`
          INNER join `contact_tags` ON `contact_tags`.`id` = `contact_contact_tags`.`id_tag`
          WHERE `contact_contact_tags`.`id_contact` = `contacts`.`id`
        "),
    ], parent::describeColumns());
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = ''; // $this->translate('Contacts');
    $description->ui['addButtonText'] = $this->translate('Add contact');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    // if ($this->router()->urlParamAsInteger('idCustomer') > 0) {
    //   $description->columns = [];
    //   $description->inputs = [];
    //   $description->ui = [];
    // }

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

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['TAGS', 'VALUES'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
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
      $helper = $this->getService(Helper::class);
      $helper->deleteTags(
        array_column($savedRecord["TAGS"], "id"),
        $this->getModel("Hubleto/App/Community/Contacts/Models/ContactTag"),
        "id_contact",
        $savedRecord["id"]
      );
    }

    return $savedRecord;
  }

}
