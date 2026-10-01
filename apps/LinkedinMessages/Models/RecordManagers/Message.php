<?php

namespace Hubleto\App\Custom\LinkedinMessages\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends \Hubleto\Erp\RecordManager
{
  public $table = 'linkedin_messages';

  /** @return BelongsTo<Account, covariant self> */
  public function ACCOUNT(): BelongsTo
  {
    return $this->belongsTo(Account::class, 'id_account', 'id');
  }

  /** @return BelongsTo<Profile, covariant self> */
  public function PROFILE(): BelongsTo
  {
    return $this->belongsTo(Profile::class, 'id_profile', 'id');
  }

  /** @return BelongsTo<Campaign, covariant self> */
  public function CAMPAIGN(): BelongsTo
  {
    return $this->belongsTo(Campaign::class, 'id_campaign', 'id');
  }

  /** @return BelongsTo<User, covariant self> */
  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

  /** @return HasMany<MessageTag, covariant self> */
  public function TAGS(): HasMany
  {
    return $this->hasMany(MessageTag::class, 'id_message', 'id');
  }

  /** @return HasMany<MessageActivity, covariant self> */
  public function ACTIVITIES(): HasMany
  {
    return $this->hasMany(MessageActivity::class, 'id_message', 'id');
  }

  /** @return HasMany<Reply, covariant self> */
  public function REPLIES(): HasMany
  {
    return $this->hasMany(Reply::class, 'id_message', 'id');
  }

  /**
   * @param mixed|null $query
   * @param int $level
   * @param array|null $includeRelations
   *
   * @return mixed
   */
  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    // tag count, used for ordering by the virtual column `virt_tags`
    $query->selectSub(function ($sub) {
      $sub->from('cross_linkedin_message_tags')
        ->join('linkedin_tags', 'linkedin_tags.id', '=', 'cross_linkedin_message_tags.id_tag')
        ->whereColumn('cross_linkedin_message_tags.id_message', 'linkedin_messages.id')
        ->selectRaw('COUNT(DISTINCT linkedin_tags.id)');
    }, 'tags_count');

    return $query;
  }

  /**
   * @param mixed $query
   *
   * @return mixed
   */
  public function addUrlFiltersToQuery(mixed $query): mixed
  {
    $query = parent::addUrlFiltersToQuery($query);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $filters = $hubleto->router()->urlParamAsArray('filters');

    if ($hubleto->router()->urlParamAsInteger('idProfile') > 0) {
      $query = $query->where($this->table . '.id_profile', $hubleto->router()->urlParamAsInteger('idProfile'));
    }
    if ($hubleto->router()->urlParamAsInteger('idCampaign') > 0) {
      $query = $query->where($this->table . '.id_campaign', $hubleto->router()->urlParamAsInteger('idCampaign'));
    }
    if ($hubleto->router()->urlParamAsInteger('idAccount') > 0) {
      $query = $query->where($this->table . '.id_account', $hubleto->router()->urlParamAsInteger('idAccount'));
    }

    $fMessageRead = (int) ($filters['fMessageRead'] ?? 0);
    if ($fMessageRead == 1) $query = $query->where($this->table . '.is_read', false);
    if ($fMessageRead == 2) $query = $query->where($this->table . '.is_read', true);

    $fMessageDirection = (int) ($filters['fMessageDirection'] ?? 0);
    if ($fMessageDirection > 0) $query = $query->where($this->table . '.direction', $fMessageDirection);

    $fMessageWithPlan = (int) ($filters['fMessageWithPlan'] ?? 0);
    if ($fMessageWithPlan == 1) {
      $query = $query->whereHas('ACTIVITIES', function ($q) {
        $q->where('completed', false);
        $q->whereDate('date_start', '>=', date('Y-m-d'));
      });
    } elseif ($fMessageWithPlan == 2) {
      $query = $query->whereDoesntHave('ACTIVITIES', function ($q) {
        $q->where('completed', false);
        $q->whereDate('date_start', '>=', date('Y-m-d'));
      });
    }

    return $query;
  }

  /**
   * @param array $dataRaw
   *
   * @return array
   */
  public function prepareLookupData(array $dataRaw): array
  {
    $data = parent::prepareLookupData($dataRaw);

    return $data;
  }

  /**
   * @param mixed $query
   * @param array $orderBy
   *
   * @return mixed
   */
  public function addOrderByToQuery(mixed $query, array $orderBy): mixed
  {
    if (($orderBy['field'] ?? null) === 'virt_tags') {
      return $query->orderBy('tags_count', $orderBy['direction']);
    }
    return parent::addOrderByToQuery($query, $orderBy);
  }
}
