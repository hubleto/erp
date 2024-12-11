<?php

namespace CeremonyCrmMod\Core\Invoices\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

use \CeremonyCrmMod\Core\Customers\Models\Eloquent\Company;
use \CeremonyCrmMod\Core\Settings\Models\Eloquent\User;
use \CeremonyCrmMod\Core\Settings\Models\Eloquent\InvoiceProfile;

class Invoice extends \ADIOS\Core\Model\Eloquent {
  public $table = 'invoices';

  public function CUSTOMER(): BelongsTo { return $this->BelongsTo(Company::class, 'id_customer'); }
  public function PROFILE(): BelongsTo { return $this->BelongsTo(InvoiceProfile::class, 'id_profile'); }
  public function ISSUED_BY(): BelongsTo { return $this->BelongsTo(User::class, 'id_issued_by'); }

  public function ITEMS(): HasMany { return $this->HasMany(InvoiceItem::class, 'id_invoice'); }

}
