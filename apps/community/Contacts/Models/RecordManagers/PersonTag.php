<?php

namespace HubletoApp\Community\Contacts\Models\RecordManagers;

use HubletoApp\Community\Contacts\Models\RecordManagers\Tag;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PersonTag extends \HubletoMain\Core\RecordManager
{
  public $table = 'cross_person_tags';

  /** @return BelongsTo<Tag, covariant PersonTag> */
  public function TAG() {
    return $this->belongsTo(Tag::class, 'id_tag', 'id');
  }

  /** @return BelongsTo<Person, covariant PersonTag> */
  public function PERSON(): BelongsTo {
    return $this->belongsTo(Person::class, 'id_person', 'id');
  }

}
