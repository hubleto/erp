<?php

namespace HubletoApp\Community\Contacts\Models\RecordManagers;

use HubletoApp\Community\Contacts\Models\RecordManagers\Tag;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ContactTag extends \Hubleto\Framework\RecordManager
{
  public $table = 'contact_contact_tags';

  /** @return BelongsTo<Tag, covariant ContactTag> */
  public function TAG()
  {
    return $this->belongsTo(Tag::class, 'id_tag', 'id');
  }

  /** @return BelongsTo<Contact, covariant ContactTag> */
  public function CONTACT(): BelongsTo
  {
    return $this->belongsTo(Contact::class, 'id_contact', 'id');
  }

}
