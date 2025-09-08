<?php

namespace Hubleto\App\Community\Contacts\Models\RecordManagers;

use Hubleto\App\Community\Settings\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tag extends \Hubleto\Erp\RecordManager
{
  public $table = 'contact_tags';
}
