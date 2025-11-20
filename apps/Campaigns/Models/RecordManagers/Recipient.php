<?php

namespace Hubleto\App\Community\Campaigns\Models\RecordManagers;

use Illuminate\Database\Eloquent\Collection;
use Hubleto\App\Community\Campaigns\Models\RecordManagers\RecipientStatus;
use Hubleto\App\Community\Contacts\Models\RecordManagers\Contact;
use Hubleto\App\Community\Mail\Models\RecordManagers\Mail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BelongsToEmail extends BelongsTo
{

  public function match(array $models, Collection $results, $relation)
  {
    $dictionary = [];

    foreach ($results as $result) {
      $attribute = strtolower($this->getDictionaryKey($this->getRelatedKeyFrom($result)));
      $dictionary[$attribute] = $result;
    }

    foreach ($models as $model) {
      $attribute = strtolower($this->getDictionaryKey($this->getForeignKeyFrom($model)));
      if (isset($dictionary[$attribute])) {
        $model->setRelation($relation, $dictionary[$attribute]);
      }
    }

    return $models;
  }

}

class Recipient extends \Hubleto\Erp\RecordManager
{
  public $table = 'campaigns_recipients';

  public function belongsToEmail($related, $foreignKey = null, $ownerKey = null, $relation = null)
  {
    $relation = $this->guessBelongsToRelation();
    $instance = $this->newRelatedInstance($related);

    return new BelongsToEmail(
      $instance->newQuery(), $this, $foreignKey, $ownerKey, $relation
    );
  }


  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function CAMPAIGN(): BelongsTo
  {
    return $this->belongsTo(Campaign::class, 'id_campaign', 'id');
  }

  /** @return BelongsTo<Contact, covariant LeadTag> */
  public function CONTACT(): BelongsTo
  {
    return $this->belongsTo(Contact::class, 'id_contact', 'id');
  }

  /** @return BelongsTo<Contact, covariant LeadTag> */
  public function MAIL(): BelongsTo
  {
    return $this->belongsTo(Mail::class, 'id_mail', 'id');
  }

  /** @return BelongsTo<Tag, covariant LeadTag> */
  public function STATUS(): BelongsToEmail
  {
    return $this->belongsToEmail(RecipientStatus::class, 'email', 'email');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    if ($hubleto->router()->isUrlParam("idCampaign")) {
      $query = $query->where($this->table . '.id_campaign', $hubleto->router()->urlParamAsInteger("idCampaign"));
    }

    return $query;
  }
}
