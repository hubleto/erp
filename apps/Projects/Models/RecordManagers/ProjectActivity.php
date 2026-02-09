<?php

namespace Hubleto\App\Community\Projects\Models\RecordManagers;

use Hubleto\App\Community\Contacts\Models\RecordManagers\Contact;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectActivity extends \Hubleto\App\Community\Calendar\Models\RecordManagers\Activity
{
  public $table = 'project_activities';

  /** @return BelongsTo<Deal, covariant ProjectActivity> */
  public function PROJECT(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'id_project', 'id');
  }

  /** @return BelongsTo<Contact, covariant Contact> */
  public function CONTACT(): BelongsTo
  {
    return $this->belongsTo(Contact::class, 'id_contact', 'id');
  }

}
