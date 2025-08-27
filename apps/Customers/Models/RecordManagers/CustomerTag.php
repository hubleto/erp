<?php

namespace HubletoApp\Community\Customers\Models\RecordManagers;

use HubletoApp\Community\Customers\Models\RecordManagers\Tag;
use HubletoApp\Community\Settings\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerTag extends \HubletoMain\RecordManager
{
  public $table = 'cross_customer_tags';

  /** @return BelongsTo<Tag, covariant CustomerTag> */
  public function TAG(): BelongsTo
  {
    return $this->belongsTo(Tag::class, 'id_tag', 'id');
  }

  /** @return BelongsTo<Customer, covariant CustomerTag> */
  public function CUSTOMER(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'id_customer', 'id');
  }

}
