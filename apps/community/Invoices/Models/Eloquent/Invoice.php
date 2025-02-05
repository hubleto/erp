<?php

namespace HubletoApp\Community\Invoices\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

use \HubletoApp\Community\Customers\Models\Eloquent\Company;
use \HubletoApp\Community\Settings\Models\Eloquent\User;
use \HubletoApp\Community\Settings\Models\Eloquent\InvoiceProfile;

class Invoice extends \HubletoMain\Core\ModelEloquent {
  public $table = 'invoices';

  /** @return BelongsTo<Company, covariant Invoice> */
  public function CUSTOMER(): BelongsTo {
    return $this->BelongsTo(Company::class, 'id_customer');
  }

  /** @return BelongsTo<InvoiceProfile, covariant Invoice> */
  public function PROFILE(): BelongsTo {
    return $this->BelongsTo(InvoiceProfile::class, 'id_profile');
  }

  /** @return BelongsTo<User, covariant Invoice> */
  public function ISSUED_BY(): BelongsTo {
    return $this->BelongsTo(User::class, 'id_issued_by');
  }

  /** @return HasMany<InvoiceItem, covariant Invoice> */
  public function ITEMS(): HasMany {
    return $this->HasMany(InvoiceItem::class, 'id_invoice');
  }

}
