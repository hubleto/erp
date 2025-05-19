<?php

namespace HubletoApp\Community\Customers\Models\RecordManagers;

use HubletoApp\Community\Customers\Models\RecordManagers\Customer;
use HubletoApp\Community\Contacts\Models\RecordManagers\Contact;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerActivity extends \HubletoMain\Core\RecordManager
{
  public $table = 'customer_activities';

  /** @return BelongsTo<Customer, covariant CustomerActivity> */
  public function CUSTOMER(): BelongsTo {
    return $this->belongsTo(Customer::class, 'id_customer', 'id');
  }

  /** @return BelongsTo<Contact, covariant CustomerActivity> */
  public function CONTACT(): BelongsTo {
    return $this->belongsTo(Contact::class, 'id_contact', 'id');
  }
}
