<?php

namespace HubletoApp\Deals\Models\Eloquent;

use HubletoApp\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DealStatus extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'deal_statuses';

}
