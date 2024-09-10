<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\Tag;
use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompanyTag extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'companies_tags';

  public function id_company(): BelongsTo
  {
    return $this->belongsTo(Company::class, "id_company", 'id');
  }
  public function id_tag(): BelongsTo
  {
    return $this->belongsTo(Tag::class, "id_tag", "id");
  }

  public function TAG() {
    return $this->id_tag();
  }

}
