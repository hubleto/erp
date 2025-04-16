<?php

namespace HubletoApp\Community\Deals\Models\RecordManagers;

use HubletoApp\Community\Customers\Models\RecordManagers\Customer;
use HubletoApp\Community\Contacts\Models\RecordManagers\Person;
use HubletoApp\Community\Deals\Models\RecordManagers\Deal;
use HubletoApp\Community\Settings\Models\RecordManagers\ActivityType;
use HubletoApp\Community\Settings\Models\RecordManagers\Currency;
use HubletoApp\Community\Settings\Models\RecordManagers\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DealHistory extends \HubletoMain\Core\RecordManager
{
  public $table = 'deal_histories';

  /** @return BelongsTo<Deal, covariant DealHistory> */
  public function DEAL(): BelongsTo {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }

}
