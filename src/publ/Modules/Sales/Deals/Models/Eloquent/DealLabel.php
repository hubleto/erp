<?php

namespace CeremonyCrmApp\Modules\Sales\Deals\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Settings\Models\Eloquent\Label;
use CeremonyCrmApp\Modules\Sales\Deals\Models\Eloquent\Deal;
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
