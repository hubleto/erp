<?php

namespace HubletoApp\Community\Contacts\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Value extends \Hubleto\Framework\RecordManager
{
  public $table = 'contact_values';

  /** @return BelongsTo<Contact, covariant Contact> */
  public function CONTACT(): BelongsTo
  {
    return $this->belongsTo(Contact::class, 'id_contact');
  }

  /** @return BelongsTo<Category, covariant Contact> */
  public function CATEGORY(): BelongsTo
  {
    return $this->belongsTo(Category::class, 'id_category', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \HubletoMain\Loader::getGlobalApp();

    if ($main->urlParamAsInteger("idContact") > 0) {
      $query = $query->where($this->table . '.id_contact', $main->urlParamAsInteger("idContact"));
    }

    return $query;
  }

}
