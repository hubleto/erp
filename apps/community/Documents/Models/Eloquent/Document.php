<?php

namespace HubletoApp\Community\Documents\Models\Eloquent;

use HubletoApp\Community\Customers\Models\Eloquent\CustomerDocument;
use HubletoApp\Community\Leads\Models\Eloquent\LeadDocument;
use HubletoApp\Community\Deals\Models\Eloquent\DealDocument;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Document extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'documents';
}
