<?php

namespace HubletoApp\Community\Documents\Models\RecordManagers;

use HubletoApp\Community\Customers\Models\RecordManagers\CustomerDocument;
use HubletoApp\Community\Leads\Models\RecordManagers\LeadDocument;
use HubletoApp\Community\Deals\Models\RecordManagers\DealDocument;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Document extends \HubletoMain\Core\RecordManager
{
  public $table = 'documents';
}
