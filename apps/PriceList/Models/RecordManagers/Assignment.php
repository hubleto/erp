<?php

namespace Hubleto\App\Community\PriceLists\Models\RecordManagers;

use Hubleto\App\Community\Cashdesk\Models\RecordManagers\CashRegister;
use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Hubleto\App\Community\Customers\Models\RecordManagers\Tag;
use Hubleto\App\Community\Deals\Models\RecordManagers\Deal;
use Hubleto\App\Community\Projects\Models\RecordManagers\Project;
use Hubleto\App\Community\Settings\Models\RecordManagers\Country;
use Hubleto\App\Community\Shops\Models\RecordManagers\Shop;
use Hubleto\App\Community\Suppliers\Models\RecordManagers\Supplier;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends \Hubleto\Erp\RecordManager
{
  public $table = 'price_lists_assignments';

  /** @return BelongsTo<PriceList, covariant Assignment> */
  public function PRICE_LIST(): BelongsTo
  {
    return $this->belongsTo(PriceList::class, 'id_price_list', 'id');
  }

  /** @return BelongsTo<Customer, covariant Assignment> */
  public function CUSTOMER(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'id_customer', 'id');
  }

  /** @return BelongsTo<Tag, covariant Assignment> */
  public function CUSTOMER_TAG(): BelongsTo
  {
    return $this->belongsTo(Tag::class, 'id_customer_tag', 'id');
  }

  /** @return BelongsTo<Country, covariant Assignment> */
  public function COUNTRY(): BelongsTo
  {
    return $this->belongsTo(Country::class, 'id_country', 'id');
  }

  /** @return BelongsTo<Supplier, covariant Assignment> */
  public function SUPPLIER(): BelongsTo
  {
    return $this->belongsTo(Supplier::class, 'id_supplier', 'id');
  }

  /** @return BelongsTo<Shop, covariant Assignment> */
  public function SHOP(): BelongsTo
  {
    return $this->belongsTo(Shop::class, 'id_shop', 'id');
  }

  /** @return BelongsTo<CashRegister, covariant Assignment> */
  public function CASH_REGISTER(): BelongsTo
  {
    return $this->belongsTo(CashRegister::class, 'id_cash_register', 'id');
  }

  /** @return BelongsTo<Deal, covariant Assignment> */
  public function DEAL(): BelongsTo
  {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }

  /** @return BelongsTo<Project, covariant Assignment> */
  public function PROJECT(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'id_project', 'id');
  }

  /**
   * Prepares the query for reading records.
   *
   * @param mixed $query
   * @param int $level
   * @param array|null $includeRelations
   *
   * @return mixed
   *
   */
  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);
    return $query;
  }

  /**
   * Adds filters given in the URL to the query.
   *
   * @param mixed $query
   *
   * @return mixed
   *
   */
  public function addUrlFiltersToQuery(mixed $query): mixed
  {
    $query = parent::addUrlFiltersToQuery($query);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $filters = $hubleto->router()->urlParamAsArray('filters');

    if ($hubleto->router()->urlParamAsInteger('idPriceList') > 0) {
      $query = $query->where($this->table . '.id_price_list', $hubleto->router()->urlParamAsInteger('idPriceList'));
    }

    if ($hubleto->router()->urlParamAsInteger('idCustomer') > 0) {
      $query = $query->where($this->table . '.id_customer', $hubleto->router()->urlParamAsInteger('idCustomer'));
    }

    if ($hubleto->router()->urlParamAsInteger('idShop') > 0) {
      $query = $query->where($this->table . '.id_shop', $hubleto->router()->urlParamAsInteger('idShop'));
    }

    if ($hubleto->router()->urlParamAsInteger('idProject') > 0) {
      $query = $query->where($this->table . '.id_project', $hubleto->router()->urlParamAsInteger('idProject'));
    }

    if ($hubleto->router()->urlParamAsInteger('idDeal') > 0) {
      $query = $query->where($this->table . '.id_deal', $hubleto->router()->urlParamAsInteger('idDeal'));
    }

    if ($hubleto->router()->urlParamAsInteger('idSupplier') > 0) {
      $query = $query->where($this->table . '.id_supplier', $hubleto->router()->urlParamAsInteger('idSupplier'));
    }

    $valuesfValidNow = (array) ($filters['fValidNow'] ?? []);
    if (count($valuesfValidNow) === 1) {
      $table = $this->table;
      $today = date('Y-m-d');
      if ((int) $valuesfValidNow[0] === 1) {
        $query = $query
          ->where(function ($q) use ($table, $today) { $q->whereNull($table . '.valid_from')->orWhere($table . '.valid_from', '<=', $today); })
          ->where(function ($q) use ($table, $today) { $q->whereNull($table . '.valid_to')->orWhere($table . '.valid_to', '>=', $today); })
        ;
      } else {
        $query = $query->where(function ($q) use ($table, $today) {
          $q->where($table . '.valid_from', '>', $today)->orWhere($table . '.valid_to', '<', $today);
        });
      }
    }

    return $query;
  }

  /**
   * Prepares data for lookup inputs.
   *
   * @param array $dataRaw
   *
   * @return array
   *
   */
  public function prepareLookupData(array $dataRaw): array
  {
    $data = parent::prepareLookupData($dataRaw);
    return $data;
  }

}
