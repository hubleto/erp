<?php

namespace Hubleto\App\Community\Campaigns\Models\RecordManagers;

use Illuminate\Database\Eloquent\Collection;
use Hubleto\App\Community\Campaigns\Models\RecordManagers\RecipientStatus;
use Hubleto\App\Community\Contacts\Models\RecordManagers\Contact;
use Hubleto\App\Community\Mail\Models\RecordManagers\Mail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

  /** @return HasMany<Contact, covariant Customer> */
  public function CLICKS(): HasMany
  {
    return $this->hasMany(Click::class, 'id_recipient');
  }

  /**
   * [Description for prepareSelectsForReadQuery]
   *
   * @param mixed|null $query
   * @param int $level
   * @param array|null|null $includeRelations
   * 
   * @return array
   * 
   */
  public function prepareSelectsForReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): array
  {
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $filters = $hubleto->router()->urlParamAsArray("filters");
    $selects = parent::prepareSelectsForReadQuery($query, $level, $includeRelations);

    if (isset($filters['fGroupBy']) && is_array($filters['fGroupBy'])) {
      $selects[] = 'count(campaigns_recipients.id) as count';
      $selects[] = '(select count(cc.id) from campaigns_clicks cc where cc.id_recipient = campaigns_recipients.id) as clicks';
    }

    return $selects;
  }

  /**
   * [Description for prepareReadQuery]
   *
   * @param mixed|null $query
   * @param int $level
   * @param array|null|null $includeRelations
   * 
   * @return mixed
   * 
   */
  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $filters = $hubleto->router()->urlParamAsArray("filters");

    if ($hubleto->router()->isUrlParam("idCampaign")) {
      $query = $query->where($this->table . '.id_campaign', $hubleto->router()->urlParamAsInteger("idCampaign"));
    }

    if (isset($filters['fGroupBy'])) {
      $fGroupBy = (array) $filters['fGroupBy'];
      if (in_array('virt_utm_source', $fGroupBy)) $query = $query->groupBy('virt_utm_source');
      if (in_array('virt_utm_campaign', $fGroupBy)) $query = $query->groupBy('virt_utm_campaign');
      if (in_array('virt_utm_term', $fGroupBy)) $query = $query->groupBy('virt_utm_term');
      if (in_array('virt_status', $fGroupBy)) $query = $query->groupBy('virt_status');
      if (in_array('email', $fGroupBy)) $query = $query->groupBy('email');
    }

    return $query;
  }
}
