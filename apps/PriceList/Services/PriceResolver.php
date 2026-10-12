<?php

namespace Hubleto\App\Community\PriceList\Services;

use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\Community\Customers\Models\CustomerTag;
use Hubleto\App\Community\Products\Models\Category;
use Hubleto\App\Community\Products\Models\Product;
use Hubleto\App\Community\Products\Models\ProductSupplier;
use Hubleto\App\Community\PriceList\Models\Assignment;
use Hubleto\App\Community\PriceList\Models\Item;
use Hubleto\App\Community\PriceList\Models\PriceList;
use Hubleto\App\Community\PriceList\Models\Rule;

/**
 * Selects the price of a product for a given context. It contains no arithmetic of its own:
 * every numeric step is delegated to PriceCalculator, VatCalculator, Rounding and CurrencyConverter.
 *
 * Query array (all keys optional except idProduct):
 *   idProduct, qty, idUnit, date (Y-m-d), type ('sales'|'purchase'),
 *   idCustomer, idSupplier, idShop, idCashRegister, idDeal, idProject, idActivityType,
 *   idCurrency (requested currency), idPriceListHint (explicit price list), vat (explicit VAT percent)
 *
 * Result array:
 *   unitPrice, unitPriceExclVat, unitPriceInclVat, basePrice, discountPercent, vatPercent, pricesIncludeVat,
 *   idCurrency, idPriceList, idItem, appliedRuleIds[], marginFloorHit, floorPrice, source, trace[]
 *   source: PRICE_LIST_ITEM | DERIVED | PRODUCT_FALLBACK | NONE
 */
class PriceResolver extends \Hubleto\Erp\Core
{
  public const MAX_DEPTH = 5;

  /** Specificity of assignment targets, highest wins. */
  protected const SPECIFICITY = [
    'deal' => 100, 'project' => 90, 'supplier' => 85, 'customer' => 80, 'customer_tag' => 70,
    'cash_register' => 60, 'shop' => 50, 'country' => 40, 'global' => 10, 'default' => 0,
  ];

  protected array $cache = [];

  /** Clears caches of products, categories and customers (called when they change). */
  public function clearCache(): void
  {
    $this->cache = [];
  }

  /**
   * Price lists applicable to the party in the query (customer, shop, supplier, ...), best match first.
   *
   * @param array $query Same keys as resolve()
   *
   * @return array List of ['idPriceList', 'code', 'name', 'via']
   *
   */
  public function getApplicableLists(array $query): array
  {
    $q = array_merge([
      'idProduct' => 0, 'qty' => 1.0, 'idUnit' => 0, 'date' => date('Y-m-d'), 'type' => 'sales',
      'idCustomer' => 0, 'idSupplier' => 0, 'idShop' => 0, 'idCashRegister' => 0, 'idDeal' => 0, 'idProject' => 0,
      'idActivityType' => 0, 'idCurrency' => 0, 'idPriceListHint' => 0, 'vat' => null,
    ], $query);

    $trace = [];
    $lists = [];
    foreach ($this->findCandidateLists($q, $this->loadContext($q), $trace) as $candidate) {
      $lists[] = [
        'idPriceList' => (int) $candidate['list']->id,
        'code' => (string) $candidate['list']->code,
        'name' => (string) $candidate['list']->name,
        'via' => (string) $candidate['via'],
      ];
    }
    return $lists;
  }

  /**
   * Resolves many queries (e.g., all items of a document).
   *
   * @param array $queries
   *
   * @return array Results in the same order
   *
   */
  public function resolveMany(array $queries): array
  {
    $results = [];
    foreach ($queries as $key => $query) $results[$key] = $this->resolve($query);
    return $results;
  }

  /**
   * Resolves the price.
   *
   * @param array $query
   *
   * @return array
   *
   */
  public function resolve(array $query): array
  {
    $q = array_merge([
      'idProduct' => 0, 'qty' => 1.0, 'idUnit' => 0, 'date' => date('Y-m-d'), 'type' => 'sales',
      'idCustomer' => 0, 'idSupplier' => 0, 'idShop' => 0, 'idCashRegister' => 0, 'idDeal' => 0, 'idProject' => 0,
      'idActivityType' => 0, 'idCurrency' => 0, 'idPriceListHint' => 0, 'vat' => null,
    ], $query);
    $q['qty'] = (float) $q['qty'];
    $q['date'] = substr((string) $q['date'], 0, 10);

    /** @var PriceCalculator $calc */
    $calc = $this->getService(PriceCalculator::class);
    /** @var Settings $settings */
    $settings = $this->getService(Settings::class);

    $trace = [];
    $context = $this->loadContext($q);
    $trace[] = 'Product #' . $q['idProduct'] . ', quantity ' . $q['qty'] . ', date ' . $q['date'] . ', type ' . $q['type'];

    $found = null;
    foreach ($this->findCandidateLists($q, $context, $trace) as $candidate) {
      $found = $this->priceFromList($candidate['list'], $q, $context, $trace, 0, []);
      if ($found !== null) {
        $trace[] = 'Price list ' . $candidate['list']->code . ' selected (' . $candidate['via'] . ').';
        break;
      }
      $trace[] = 'Price list ' . $candidate['list']->code . ' gave no price (' . $candidate['via'] . ').';
    }

    $source = $found['source'] ?? 'NONE';
    $list = $found['list'] ?? null;
    $unitPrice = $found['unitPrice'] ?? 0.0;
    $basePrice = $found['basePrice'] ?? 0.0;
    $idCurrency = $list ? (int) $list->id_currency : 0;
    $pricesIncludeVat = $list ? (bool) $list->prices_include_vat : false;

    if ($found === null && $settings->getBool('resolution.fallbackToProductPrice')) {
      $fallback = $this->productFallback($q, $context);
      if ($fallback !== null) {
        $found = ['unitPrice' => $fallback, 'basePrice' => $fallback, 'source' => 'PRODUCT_FALLBACK', 'idItem' => 0, 'list' => null, 'marginFloorHit' => false];
        $unitPrice = $fallback;
        $basePrice = $fallback;
        $source = 'PRODUCT_FALLBACK';
        $trace[] = 'No price list gave a price, product price used.';
      }
    }

    if ($found === null) {
      $trace[] = 'No price found.';
      return $this->emptyResult($q, $trace);
    }

    // rules (discounts and surcharges)
    $appliedRuleIds = [];
    $discountPercents = [];
    foreach ($this->findRules($list, $q, $context, $unitPrice) as $rule) {
      $appliedRuleIds[] = (int) $rule->id;
      switch ((int) $rule->kind) {
        case Rule::KIND_PERCENT_OFF:
          $discountPercents[] = (float) $rule->value;
        break;
        case Rule::KIND_AMOUNT_OFF:
          $unitPrice = $calc->subtractAmount($unitPrice, (float) $rule->value);
        break;
        case Rule::KIND_PERCENT_SURCHARGE:
          $unitPrice = $calc->applyPercentOn($unitPrice, (float) $rule->value);
        break;
        case Rule::KIND_AMOUNT_SURCHARGE:
          $unitPrice = $calc->addAmount($unitPrice, (float) $rule->value);
        break;
        case Rule::KIND_SET_PRICE:
          $unitPrice = (float) $rule->value;
        break;
      }
      $trace[] = 'Rule "' . $rule->name . '" applied.';
      if (!(bool) $rule->is_stackable) break;
    }
    $discountPercent = empty($discountPercents) ? 0.0 : $calc->combineDiscountPercents($discountPercents);

    // currency
    $requestedCurrency = (int) $q['idCurrency'];
    if ($requestedCurrency > 0 && $idCurrency > 0 && $requestedCurrency !== $idCurrency) {
      /** @var CurrencyConverter $converter */
      $converter = $this->getService(CurrencyConverter::class);
      $converted = $converter->convert($unitPrice, $idCurrency, $requestedCurrency, $q['date']);
      if ($converted === null) {
        $trace[] = 'No exchange rate between currencies #' . $idCurrency . ' and #' . $requestedCurrency . '; price not converted.';
      } else {
        $unitPrice = $converted;
        $basePrice = (float) $converter->convert($basePrice, $idCurrency, $requestedCurrency, $q['date']);
        $idCurrency = $requestedCurrency;
        $trace[] = 'Converted to currency #' . $requestedCurrency . '.';
      }
    }

    // rounding of the list
    if ($list) {
      $unitPrice = $calc->round($unitPrice, [
        'policy' => (int) $list->rounding_policy,
        'step' => (float) $list->rounding_step,
        'ending' => (float) $list->rounding_ending,
        'decimals' => $settings->getInt('calc.decimals.unit'),
      ]);
    }

    // VAT
    /** @var VatCalculator $vatCalculator */
    $vatCalculator = $this->getService(VatCalculator::class);
    $rate = $vatCalculator->resolveRate([
      'vat' => $q['vat'], 'idPrice' => (int) ($found['idItem'] ?? 0), 'idProduct' => (int) $q['idProduct'],
      'idCustomer' => (int) $q['idCustomer'], 'date' => $q['date'],
    ]);
    $vatPercent = (float) $rate['percent'];

    return [
      'unitPrice' => $unitPrice,
      'unitPriceExclVat' => $calc->convertVatMode($unitPrice, $vatPercent, $pricesIncludeVat, false),
      'unitPriceInclVat' => $calc->convertVatMode($unitPrice, $vatPercent, $pricesIncludeVat, true),
      'basePrice' => $basePrice,
      'discountPercent' => $discountPercent,
      'vatPercent' => $vatPercent,
      'vatNote' => $rate['note'],
      'pricesIncludeVat' => $pricesIncludeVat,
      'idCurrency' => $idCurrency,
      'idPriceList' => $list ? (int) $list->id : 0,
      'idItem' => (int) ($found['idItem'] ?? 0),
      'appliedRuleIds' => $appliedRuleIds,
      'marginFloorHit' => (bool) ($found['marginFloorHit'] ?? false),
      'floorPrice' => $found['floorPrice'] ?? null,
      'source' => $source,
      'trace' => $trace,
    ];
  }

  // -----------------------------------------------------------------------------------------------------------------
  // context
  // -----------------------------------------------------------------------------------------------------------------

  /** Loads product, category chain, customer country and tags (cached per request). */
  protected function loadContext(array $q): array
  {
    $context = ['product' => null, 'categoryChain' => [], 'idGroup' => 0, 'idCountry' => 0, 'tagIds' => []];

    if ((int) $q['idProduct'] > 0) {
      $key = 'product:' . $q['idProduct'];
      if (!isset($this->cache[$key])) {
        /** @var Product $mProduct */
        $mProduct = $this->getModel(Product::class);
        $this->cache[$key] = $mProduct->record->where($mProduct->table . '.id', (int) $q['idProduct'])->first();
      }
      $product = $this->cache[$key];
      $context['product'] = $product;
      if ($product) {
        $context['idGroup'] = (int) $product->id_group;
        $context['categoryChain'] = $this->getCategoryChain((int) $product->id_category);
      }
    }

    if ((int) $q['idCustomer'] > 0) {
      $key = 'customer:' . $q['idCustomer'];
      if (!isset($this->cache[$key])) {
        /** @var Customer $mCustomer */
        $mCustomer = $this->getModel(Customer::class);
        $customer = $mCustomer->record->where($mCustomer->table . '.id', (int) $q['idCustomer'])->first();
        /** @var CustomerTag $mCustomerTag */
        $mCustomerTag = $this->getModel(CustomerTag::class);
        $tagIds = $mCustomerTag->record->where($mCustomerTag->table . '.id_customer', (int) $q['idCustomer'])->pluck('id_tag')->toArray();
        $this->cache[$key] = ['idCountry' => (int) ($customer->id_country ?? 0), 'tagIds' => array_map('intval', $tagIds)];
      }
      $context['idCountry'] = $this->cache[$key]['idCountry'];
      $context['tagIds'] = $this->cache[$key]['tagIds'];
    }

    return $context;
  }

  /** Category and all its ancestors, nearest first. */
  protected function getCategoryChain(int $idCategory): array
  {
    $chain = [];
    $visited = [];
    /** @var Category $mCategory */
    $mCategory = $this->getModel(Category::class);
    while ($idCategory > 0 && !isset($visited[$idCategory])) {
      $visited[$idCategory] = true;
      $chain[] = $idCategory;
      $key = 'category:' . $idCategory;
      if (!isset($this->cache[$key])) {
        $this->cache[$key] = (int) ($mCategory->record->where($mCategory->table . '.id', $idCategory)->first()?->id_parent ?? 0);
      }
      $idCategory = $this->cache[$key];
    }
    return $chain;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // candidate lists
  // -----------------------------------------------------------------------------------------------------------------

  /** Active, valid price lists of the requested type ordered by the specificity of their assignment. */
  protected function findCandidateLists(array $q, array $context, array &$trace): array
  {
    /** @var PriceList $mList */
    $mList = $this->getModel(PriceList::class);
    $type = $q['type'] === 'purchase' ? PriceList::TYPE_PURCHASE : PriceList::TYPE_SALES;
    $date = $q['date'];
    $table = $mList->table;

    $validList = function ($query) use ($table, $date, $type) {
      return $query
        ->where($table . '.status', PriceList::STATUS_ACTIVE)
        ->where($table . '.type', $type)
        ->where(function ($sub) use ($table, $date) { $sub->whereNull($table . '.valid_from')->orWhere($table . '.valid_from', '<=', $date); })
        ->where(function ($sub) use ($table, $date) { $sub->whereNull($table . '.valid_to')->orWhere($table . '.valid_to', '>=', $date); })
      ;
    };

    if ((int) $q['idPriceListHint'] > 0) {
      $list = $validList($mList->record)->where($table . '.id', (int) $q['idPriceListHint'])->first();
      if (!$list) {
        $trace[] = 'The requested price list #' . $q['idPriceListHint'] . ' is not active or valid.';
        return [];
      }
      return [['list' => $list, 'via' => 'requested']];
    }

    $candidates = [];

    /** @var Assignment $mAssignment */
    $mAssignment = $this->getModel(Assignment::class);
    $aTable = $mAssignment->table;
    $assignments = $mAssignment->record
      ->where(function ($sub) use ($aTable, $date) { $sub->whereNull($aTable . '.valid_from')->orWhere($aTable . '.valid_from', '<=', $date); })
      ->where(function ($sub) use ($aTable, $date) { $sub->whereNull($aTable . '.valid_to')->orWhere($aTable . '.valid_to', '>=', $date); })
      ->get()
    ;

    foreach ($assignments as $assignment) {
      $kind = $this->matchAssignment($assignment, $q, $context);
      if ($kind === null) continue;

      $list = $validList($mList->record)->where($table . '.id', (int) $assignment->id_price_list)->first();
      if (!$list) continue;

      $score = [self::SPECIFICITY[$kind], (int) $assignment->priority, (int) $list->priority, (string) $list->valid_from, (int) $list->id];
      $listId = (int) $list->id;
      if (!isset($candidates[$listId]) || $candidates[$listId]['score'] < $score) {
        $candidates[$listId] = ['list' => $list, 'via' => 'assignment: ' . $kind, 'score' => $score];
      }
    }

    uasort($candidates, fn($a, $b) => $b['score'] <=> $a['score']);
    $result = array_values($candidates);

    $default = $validList($mList->record)->where($table . '.is_default', 1)->orderBy($table . '.priority', 'desc')->first();
    if ($default && !isset($candidates[(int) $default->id])) {
      $result[] = ['list' => $default, 'via' => 'default list'];
    }

    return $result;
  }

  /** Returns the kind of the assignment target if it matches the query, otherwise null. */
  protected function matchAssignment(mixed $a, array $q, array $context): ?string
  {
    $targets = [
      'id_deal' => ['deal', (int) $q['idDeal']],
      'id_project' => ['project', (int) $q['idProject']],
      'id_supplier' => ['supplier', (int) $q['idSupplier']],
      'id_customer' => ['customer', (int) $q['idCustomer']],
      'id_cash_register' => ['cash_register', (int) $q['idCashRegister']],
      'id_shop' => ['shop', (int) $q['idShop']],
      'id_country' => ['country', (int) $context['idCountry']],
    ];

    foreach ($targets as $column => [$kind, $queryValue]) {
      if ((int) $a->{$column} > 0) return ((int) $a->{$column} === $queryValue && $queryValue > 0) ? $kind : null;
    }

    if ((int) $a->id_customer_tag > 0) {
      return in_array((int) $a->id_customer_tag, $context['tagIds']) ? 'customer_tag' : null;
    }

    return 'global';
  }

  // -----------------------------------------------------------------------------------------------------------------
  // price from a list
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Price of the product in one list: a matching price (item) of the list, or the price derived from the list's basis.
   *
   * @return array|null ['unitPrice', 'basePrice', 'source', 'idItem', 'list', 'marginFloorHit']
   */
  protected function priceFromList(mixed $list, array $q, array $context, array &$trace, int $depth, array $visited): ?array
  {
    if ($depth > self::MAX_DEPTH || isset($visited[(int) $list->id])) {
      $trace[] = 'Price list ' . $list->code . ' skipped (cycle or too deep).';
      return null;
    }
    $visited[(int) $list->id] = true;

    $item = $this->findItem($list, $q, $context);
    if ($item !== null) return $this->priceFromItem($list, $item, $q, $context, $trace, $depth, $visited);

    $base = $this->priceFromBasis($list, $q, $context, $trace, $depth, $visited);
    if ($base === null) return null;

    /** @var PriceCalculator $calc */
    $calc = $this->getService(PriceCalculator::class);
    $unitPrice = $base;
    $value = (float) $list->derive_value;
    switch ((int) $list->derive_method) {
      case PriceList::DERIVE_PERCENT_OFF: $unitPrice = $calc->calculateDiscountedPrice($base, $value); break;
      case PriceList::DERIVE_PERCENT_ON: $unitPrice = $calc->applyPercentOn($base, $value); break;
      case PriceList::DERIVE_AMOUNT_OFF: $unitPrice = $calc->subtractAmount($base, $value); break;
      case PriceList::DERIVE_AMOUNT_ON: $unitPrice = $calc->addAmount($base, $value); break;
    }

    return ['unitPrice' => $unitPrice, 'basePrice' => $base, 'source' => 'DERIVED', 'idItem' => 0, 'list' => $list, 'marginFloorHit' => false];
  }

  /** Base price given by the basis of the list, or null for lists with only their own prices. */
  protected function priceFromBasis(mixed $list, array $q, array $context, array &$trace, int $depth, array $visited): ?float
  {
    switch ((int) $list->basis) {
      case PriceList::BASIS_PRODUCT_SALES_PRICE:
        return $context['product'] ? (float) $context['product']->sales_price : null;
      case PriceList::BASIS_PRODUCT_PURCHASE_PRICE:
        return $this->productCost($q);
      case PriceList::BASIS_PARENT_LIST:
        return $this->priceFromParent((int) $list->id_parent_price_list, $q, $context, $trace, $depth, $visited);
    }
    return null;
  }

  protected function priceFromParent(int $idParent, array $q, array $context, array &$trace, int $depth, array $visited): ?float
  {
    if ($idParent <= 0) return null;
    /** @var PriceList $mList */
    $mList = $this->getModel(PriceList::class);
    $parent = $mList->record->where($mList->table . '.id', $idParent)->first();
    if (!$parent) return null;
    $result = $this->priceFromList($parent, $q, $context, $trace, $depth + 1, $visited);
    return $result === null ? null : (float) $result['unitPrice'];
  }

  /** Cheapest purchase price of the product (of the given supplier if known). */
  protected function productCost(array $q): ?float
  {
    if ((int) $q['idProduct'] <= 0) return null;

    /** @var ProductSupplier $mPs */
    $mPs = $this->getModel(ProductSupplier::class);
    $query = $mPs->record->where($mPs->table . '.id_product', (int) $q['idProduct'])->where($mPs->table . '.purchase_price', '>', 0);
    if ((int) $q['idSupplier'] > 0) $query = $query->where($mPs->table . '.id_supplier', (int) $q['idSupplier']);

    $cost = $query->min($mPs->table . '.purchase_price');
    return $cost === null ? null : (float) $cost;
  }

  protected function productFallback(array $q, array $context): ?float
  {
    if ($q['type'] === 'purchase') return $this->productCost($q);
    return $context['product'] ? (float) $context['product']->sales_price : null;
  }

  /**
   * The best matching price of the list: most specific scope, then highest minimum quantity, priority and newest validity.
   */
  protected function findItem(mixed $list, array $q, array $context): mixed
  {
    /** @var Item $mItem */
    $mItem = $this->getModel(Item::class);
    $t = $mItem->table;
    $date = $q['date'];
    $qty = (float) $q['qty'];
    $chain = $context['categoryChain'];
    $idProduct = (int) $q['idProduct'];
    $idGroup = (int) $context['idGroup'];
    $idActivityType = (int) $q['idActivityType'];

    $items = $mItem->record
      ->where($t . '.id_price_list', (int) $list->id)
      ->where($t . '.is_active', 1)
      ->where(function ($s) use ($t, $date) { $s->whereNull($t . '.valid_from')->orWhere($t . '.valid_from', '<=', $date); })
      ->where(function ($s) use ($t, $date) { $s->whereNull($t . '.valid_to')->orWhere($t . '.valid_to', '>=', $date); })
      ->where(function ($s) use ($t, $qty) { $s->whereNull($t . '.min_qty')->orWhere($t . '.min_qty', '<=', $qty); })
      ->where(function ($s) use ($t, $qty) { $s->whereNull($t . '.max_qty')->orWhere($t . '.max_qty', '<=', 0)->orWhere($t . '.max_qty', '>=', $qty); })
      ->get()
    ;

    $best = null;
    $bestScore = null;
    foreach ($items as $item) {
      $scope = $this->itemScopeScore($item, $idProduct, $chain, $idGroup, $idActivityType);
      if ($scope === null) continue;

      if ((int) $item->id_unit > 0 && (int) $item->id_unit !== (int) $q['idUnit']) continue;
      if ((int) $item->id_supplier > 0 && (int) $item->id_supplier !== (int) $q['idSupplier']) continue;

      $score = [$scope, (float) $item->min_qty, (int) $item->priority, (string) $item->valid_from, (int) $item->id];
      if ($bestScore === null || $score > $bestScore) {
        $best = $item;
        $bestScore = $score;
      }
    }

    return $best;
  }

  /** Scope specificity of a price: 40 product or activity type, 30-x category (nearest first), 20 group, 10 whole list; null if it does not match. */
  protected function itemScopeScore(mixed $item, int $idProduct, array $categoryChain, int $idGroup, int $idActivityType): ?int
  {
    if ((int) $item->id_product > 0) return (int) $item->id_product === $idProduct ? 40 : null;
    if ((int) $item->id_category > 0) {
      $position = array_search((int) $item->id_category, $categoryChain, true);
      return $position === false ? null : 30 - (int) $position;
    }
    if ((int) $item->id_group > 0) return ((int) $item->id_group === $idGroup && $idGroup > 0) ? 20 : null;
    return 10;
  }

  /** Computes the unit price of a matching price (item) of the list. */
  protected function priceFromItem(mixed $list, mixed $item, array $q, array $context, array &$trace, int $depth, array $visited): ?array
  {
    /** @var PriceCalculator $calc */
    $calc = $this->getService(PriceCalculator::class);

    $cost = null;
    $base = null;
    switch ((int) $item->base) {
      case Item::BASE_PRODUCT_SALES_PRICE:
        $base = $context['product'] ? (float) $context['product']->sales_price : null;
      break;
      case Item::BASE_PRODUCT_COST:
        $base = $this->productCost($q);
      break;
      case Item::BASE_PARENT_LIST:
        $base = $this->priceFromParent((int) $list->id_parent_price_list, $q, $context, $trace, $depth, $visited);
      break;
      case Item::BASE_OTHER_LIST:
        $base = $this->priceFromParent((int) $item->id_base_price_list, $q, $context, $trace, $depth, $visited);
      break;
    }

    $value = (float) $item->value;
    $method = (int) $item->method;
    $costMethods = [Item::METHOD_MARKUP_ON_COST, Item::METHOD_MARGIN_ON_COST];

    if (in_array($method, $costMethods)) {
      $cost = $this->productCost($q);
      $base = $cost;
    }

    if ($method !== Item::METHOD_FIXED && $base === null) {
      $trace[] = 'Price #' . $item->id . ' skipped: its base price is not known.';
      return null;
    }

    $unitPrice = $value;
    try {
      switch ($method) {
        case Item::METHOD_PERCENT_OFF_BASE: $unitPrice = $calc->calculateDiscountedPrice((float) $base, $value); break;
        case Item::METHOD_AMOUNT_OFF_BASE: $unitPrice = $calc->subtractAmount((float) $base, $value); break;
        case Item::METHOD_MARKUP_ON_COST: $unitPrice = $calc->priceFromCostMarkup((float) $cost, $value); break;
        case Item::METHOD_MARGIN_ON_COST: $unitPrice = $calc->priceFromCostMargin((float) $cost, $value); break;
      }
    } catch (\InvalidArgumentException $e) {
      $trace[] = 'Price #' . $item->id . ' skipped: ' . $e->getMessage();
      return null;
    }

    if ((float) $item->extra_fee != 0.0) $unitPrice = $calc->addAmount($unitPrice, (float) $item->extra_fee);

    // floors and caps
    $marginFloorHit = false;
    $floorPrice = null;
    if ((float) $item->min_margin_percent > 0) {
      $cost = $cost ?? $this->productCost($q);
      if ($cost !== null) {
        try {
          $floor = $calc->priceFromCostMargin($cost, (float) $item->min_margin_percent);
          $floorPrice = $floor;
          if ($unitPrice < $floor) {
            $unitPrice = $floor;
            $marginFloorHit = true;
            $trace[] = 'Minimum margin applied.';
          }
        } catch (\InvalidArgumentException $e) {
          $trace[] = 'Minimum margin ignored: ' . $e->getMessage();
        }
      }
    }
    if ((float) $item->min_price > 0) {
      $floorPrice = $floorPrice === null ? (float) $item->min_price : max($floorPrice, (float) $item->min_price);
    }
    if ((float) $item->min_price > 0 && $unitPrice < (float) $item->min_price) {
      $unitPrice = (float) $item->min_price;
      $trace[] = 'Minimum price applied.';
    }
    if ((float) $item->max_price > 0 && $unitPrice > (float) $item->max_price) {
      $unitPrice = (float) $item->max_price;
      $trace[] = 'Maximum price applied.';
    }

    $trace[] = 'Price #' . $item->id . ' matched (method ' . $method . ').';

    return [
      'unitPrice' => $unitPrice,
      'basePrice' => $base ?? $unitPrice,
      'source' => 'PRICE_LIST_ITEM',
      'idItem' => (int) $item->id,
      'list' => $list,
      'marginFloorHit' => $marginFloorHit,
      'floorPrice' => $floorPrice,
    ];
  }

  // -----------------------------------------------------------------------------------------------------------------
  // rules
  // -----------------------------------------------------------------------------------------------------------------

  /** Rules matching the query, highest priority first. */
  protected function findRules(mixed $list, array $q, array $context, float $unitPrice): array
  {
    /** @var Rule $mRule */
    $mRule = $this->getModel(Rule::class);
    $t = $mRule->table;
    $date = $q['date'];
    $qty = (float) $q['qty'];
    $idList = $list ? (int) $list->id : 0;

    /** @var PriceCalculator $calc */
    $calc = $this->getService(PriceCalculator::class);
    $amount = $calc->calculateFullPrice($unitPrice, $qty);

    $rules = $mRule->record
      ->where($t . '.is_active', 1)
      ->where(function ($s) use ($t, $idList) { $s->whereNull($t . '.id_price_list')->orWhere($t . '.id_price_list', $idList); })
      ->where(function ($s) use ($t, $date) { $s->whereNull($t . '.valid_from')->orWhere($t . '.valid_from', '<=', $date); })
      ->where(function ($s) use ($t, $date) { $s->whereNull($t . '.valid_to')->orWhere($t . '.valid_to', '>=', $date); })
      ->orderBy($t . '.priority', 'desc')
      ->orderBy($t . '.id', 'asc')
      ->get()
    ;

    $matching = [];
    foreach ($rules as $rule) {
      if ((int) $rule->id_product > 0 && (int) $rule->id_product !== (int) $q['idProduct']) continue;
      if ((int) $rule->id_category > 0 && !in_array((int) $rule->id_category, $context['categoryChain'])) continue;
      if ((int) $rule->id_group > 0 && (int) $rule->id_group !== (int) $context['idGroup']) continue;
      if ((int) $rule->id_customer > 0 && (int) $rule->id_customer !== (int) $q['idCustomer']) continue;
      if ((int) $rule->id_customer_tag > 0 && !in_array((int) $rule->id_customer_tag, $context['tagIds'])) continue;
      if ((float) $rule->min_qty > 0 && $qty < (float) $rule->min_qty) continue;
      if ((float) $rule->max_qty > 0 && $qty > (float) $rule->max_qty) continue;
      if ((float) $rule->min_amount > 0 && $amount < (float) $rule->min_amount) continue;
      if ((float) $rule->max_amount > 0 && $amount > (float) $rule->max_amount) continue;
      $matching[] = $rule;
    }

    return $matching;
  }

  protected function emptyResult(array $q, array $trace): array
  {
    return [
      'unitPrice' => 0.0, 'unitPriceExclVat' => 0.0, 'unitPriceInclVat' => 0.0, 'basePrice' => 0.0, 'discountPercent' => 0.0,
      'vatPercent' => 0.0, 'vatNote' => '', 'pricesIncludeVat' => false, 'idCurrency' => (int) $q['idCurrency'],
      'idPriceList' => 0, 'idItem' => 0, 'appliedRuleIds' => [], 'marginFloorHit' => false, 'floorPrice' => null, 'source' => 'NONE', 'trace' => $trace,
    ];
  }
}
