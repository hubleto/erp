<?php

namespace HubletoApp\Community\Contacts\Models\RecordManagers;

use HubletoApp\Community\Settings\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends \HubletoMain\RecordManager
{
  public $table = 'contact_categories';

}
