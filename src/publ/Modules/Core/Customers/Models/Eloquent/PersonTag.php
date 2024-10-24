<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\Tag;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PersonTag extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'person_tags';

  public function TAG() {
    return $this->belongsTo(Tag::class, 'id_tag', 'id');
  }
  public function PERSON(): BelongsTo {
    return $this->belongsTo(Person::class, 'id_person', 'id');
  }

}
