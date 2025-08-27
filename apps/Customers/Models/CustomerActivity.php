<?php

namespace HubletoApp\Community\Customers\Models;

use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Contacts\Models\Contact;

class CustomerActivity extends \HubletoApp\Community\Calendar\Models\Activity
{
  public string $table = 'customer_activities';
  public string $recordManagerClass = RecordManagers\CustomerActivity::class;

  public array $relations = [
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, 'id_customer', 'id' ],
    'CONTACT' => [ self::BELONGS_TO, Contact::class, 'id_contact', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setRequired(),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class)),
    ]);
  }

}
