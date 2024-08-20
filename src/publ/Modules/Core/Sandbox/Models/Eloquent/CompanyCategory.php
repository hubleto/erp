<?php

namespace CeremonyCrmApp\Modules\Core\Sandbox\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyCategory extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'sbx_companies_categories';

  public function id_company(): BelongsTo
  {
    return $this->belongsTo(Company::class, 'id_company', 'id');
  }

  public function id_category(): BelongsTo
  {
    return $this->belongsTo(Category::class, 'id_category', 'id');
  }

  public function CATEGORY(): BelongsTo
  {
    return $this->id_category();
  }

}
