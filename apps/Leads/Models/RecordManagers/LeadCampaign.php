<?php

namespace HubletoApp\Community\Leads\Models\RecordManagers;

use HubletoApp\Community\Campaigns\Models\RecordManagers\Campaign;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadCampaign extends \HubletoMain\RecordManager
{
  public $table = 'leads_campaigns';

  /** @return BelongsTo<Product, covariant OrderProduct> */
  public function LEAD(): BelongsTo
  {
    return $this->belongsTo(Lead::class, 'id_lead', 'id');
  }

  /** @return BelongsTo<Campaign, covariant CampaignProduct> */
  public function CAMPAIGN(): BelongsTo
  {
    return $this->belongsTo(Campaign::class, 'id_campaign', 'id');
  }

}
