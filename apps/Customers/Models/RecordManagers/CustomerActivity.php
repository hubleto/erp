<?php

namespace Hubleto\App\Community\Customers\Models\RecordManagers;

use Hubleto\App\Community\Contacts\Models\RecordManagers\Contact;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerActivity extends \Hubleto\App\Community\Calendar\Models\RecordManagers\Activity
{
  public $table = 'customer_activities';

  /** @return BelongsTo<Customer, covariant CustomerActivity> */
  public function CUSTOMER(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'id_customer', 'id');
  }

  /** @return BelongsTo<Contact, covariant CustomerActivity> */
  public function CONTACT(): BelongsTo
  {
    return $this->belongsTo(Contact::class, 'id_contact', 'id');
  }
}
