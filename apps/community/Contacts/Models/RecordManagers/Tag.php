<?php

namespace HubletoApp\Community\Contacts\Models\RecordManagers;

use HubletoApp\Community\Settings\Models\RecordManagers\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tag extends \HubletoMain\Core\RecordManager
{
  public $table = 'contact_tags';
}
