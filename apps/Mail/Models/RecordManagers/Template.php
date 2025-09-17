<?php

namespace Hubleto\App\Community\Mail\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;

class Template extends \Hubleto\Erp\RecordManager
{
  public $table = 'mails_templates';

}
