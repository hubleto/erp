<?php

namespace CeremonyCrmMod\Deals\Models\Eloquent;

use CeremonyCrmMod\Settings\Models\Eloquent\Label;
use CeremonyCrmMod\Deals\Models\Eloquent\Deal;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DealLabel extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'deal_labels';

  public function DEAL(): BelongsTo {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }
  public function LABEL(): BelongsTo {
    return $this->belongsTo(Label::class, 'id_label', 'id');
  }

}
