<?php

namespace Hubleto\App\Community\PriceLists;

use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\Community\Customers\Models\Tag;
use Hubleto\App\Community\Products\Models\Product;
use Hubleto\App\Community\Products\Models\ProductSupplier;
use Hubleto\App\Community\Settings\Models\Currency;
use Hubleto\App\Community\Settings\Services\Currency as CurrencyService;
use Hubleto\App\Community\Suppliers\Models\Supplier;
use Hubleto\App\Community\Workflow\Models\Workflow as WorkflowModel;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\App\Community\PriceLists\Services\Counter;
use Hubleto\App\Community\PriceLists\Services\PriceCalculator;

class Loader extends \Hubleto\Erp\App
{

  /**
   * Inits the app: adds routes, settings, calendars, boards, workflow, event listeners and crons.
   *
   * @return void
   *
   */
  public function init(): void
  {
    parent::init();

    $this->router()->crud('price-lists', Controllers\PriceLists::class);
    $this->router()->crud('price-lists/items', Controllers\Items::class);
    $this->router()->crud('price-lists/assignments', Controllers\Assignments::class);
    $this->router()->crud('price-lists/rules', Controllers\Rules::class);
    $this->router()->crud('price-lists/histories', Controllers\Histories::class);
    $this->router()->crud('price-lists/resolutions', Controllers\Resolutions::class);
    $this->router()->crud('price-lists/documents', Controllers\PriceListHasDocuments::class);

    $this->router()->get([
      '/^settings\/price-lists\/?$/' => Controllers\Settings::class,
      '/^price-lists\/simulator\/?$/' => Controllers\Simulator::class,
      '/^price-lists\/matrix\/?$/' => Controllers\Matrix::class,

      '/^price-lists\/board\/expiring\/?$/' => Controllers\Boards\ExpiringPriceLists::class,
      '/^price-lists\/board\/margin-warnings\/?$/' => Controllers\Boards\MarginWarnings::class,

      '/^price-lists\/api\/resolve-price\/?$/' => Controllers\Api\ResolvePrice::class,
      '/^price-lists\/api\/resolve-prices\/?$/' => Controllers\Api\ResolvePrices::class,
      '/^price-lists\/api\/explain-price\/?$/' => Controllers\Api\ExplainPrice::class,
      '/^price-lists\/api\/apply-to-document\/?$/' => Controllers\Api\ApplyToDocument::class,
      '/^price-lists\/api\/calculate-item\/?$/' => Controllers\Api\CalculateItem::class,
      '/^price-lists\/api\/calculate-items\/?$/' => Controllers\Api\CalculateItems::class,
      '/^price-lists\/api\/convert-vat\/?$/' => Controllers\Api\ConvertVat::class,
      '/^price-lists\/api\/get-vat-rate\/?$/' => Controllers\Api\GetVatRate::class,
      '/^price-lists\/api\/get-price-lists-for-party\/?$/' => Controllers\Api\GetPriceListsForParty::class,
      '/^price-lists\/api\/copy-price-list\/?$/' => Controllers\Api\CopyPriceList::class,
      '/^price-lists\/api\/bulk-adjust\/?$/' => Controllers\Api\BulkAdjust::class,
      '/^price-lists\/api\/set-status\/?$/' => Controllers\Api\SetStatus::class,
      '/^price-lists\/api\/sync-supplier-prices\/?$/' => Controllers\Api\SyncSupplierPrices::class,
      '/^price-lists\/api\/generate-pdf\/?$/' => Controllers\Api\GeneratePdf::class,
      '/^price-lists\/api\/send-by-email\/?$/' => Controllers\Api\SendByEmail::class,
      '/^price-lists\/api\/get-matrix\/?$/' => Controllers\Api\GetMatrix::class,
      '/^price-lists\/api\/set-matrix-price\/?$/' => Controllers\Api\SetMatrixPrice::class,
    ]);

    $this->addSearchSwitch('pl', 'price lists');

    /** @var \Hubleto\App\Community\Settings\Loader $settingsApp */
    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Price lists'),
      'icon' => 'fas fa-tags',
      'url' => 'settings/price-lists',
    ]);

    /** @var \Hubleto\App\Community\Workflow\Manager $workflowManager */
    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflowGroup($this, 'price-lists', Workflow::class);

    /** @var \Hubleto\App\Community\Calendar\Manager $calendarManager */
    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'price-lists', Calendar::class);

    /** @var \Hubleto\App\Community\Dashboards\Manager $dashboardManager */
    $dashboardManager = $this->getService(\Hubleto\App\Community\Dashboards\Manager::class);
    $dashboardManager->addBoard($this, $this->translate('Expiring price lists'), 'price-lists/board/expiring');
    $dashboardManager->addBoard($this, $this->translate('Margin warnings'), 'price-lists/board/margin-warnings');

    // event listeners (every listener returns on its first line unless the model is of interest)
    /** @var EventListeners\ApplyAndAuditItemPrice $applyAndAudit */
    $applyAndAudit = $this->getService(EventListeners\ApplyAndAuditItemPrice::class);
    foreach (['onModelBeforeCreate', 'onModelBeforeUpdate', 'onModelAfterCreate', 'onModelAfterUpdate'] as $event) {
      $this->eventManager()->addEventListener($event, $applyAndAudit);
    }

    /** @var EventListeners\TrackProductPriceChange $trackProduct */
    $trackProduct = $this->getService(EventListeners\TrackProductPriceChange::class);
    $this->eventManager()->addEventListener('onModelAfterUpdate', $trackProduct);

    /** @var EventListeners\ReactToSupplierPrice $supplierPrice */
    $supplierPrice = $this->getService(EventListeners\ReactToSupplierPrice::class);
    foreach (['onModelAfterCreate', 'onModelAfterUpdate', 'onModelAfterDelete'] as $event) {
      $this->eventManager()->addEventListener($event, $supplierPrice);
    }

    /** @var EventListeners\InvalidateScopeCache $invalidateCache */
    $invalidateCache = $this->getService(EventListeners\InvalidateScopeCache::class);
    foreach (['onModelAfterCreate', 'onModelAfterUpdate', 'onModelAfterDelete'] as $event) {
      $this->eventManager()->addEventListener($event, $invalidateCache);
    }

    // crons
    $this->cronManager()->addCron(Crons\ActivateAndExpire::class);
    $this->cronManager()->addCron(Crons\WarnAboutExpiry::class);
  }

  /**
   * Installs the app: creates tables (round 1) and seeds default data (round 2).
   *
   * @param int $round
   *
   * @return void
   *
   */
  public function installApp(int $round): void
  {
    if ($round == 1) {
      $modelClasses = [
        Models\PriceList::class, Models\Item::class,
        Models\Assignment::class, Models\Rule::class, Models\History::class, Models\Resolution::class,
        Models\PriceListHasDocument::class,
      ];
      foreach ($modelClasses as $modelClass) {
        /** @var \Hubleto\Erp\Model $model */
        $model = $this->getModel($modelClass);
        $model->upgradeSchema();
      }
    }

    if ($round == 2) {
      // workflow of price lists (the other apps' calculators are NOT touched on install; see the Settings page)
      /** @var WorkflowModel $mWorkflow */
      $mWorkflow = $this->getModel(WorkflowModel::class);
      if ($mWorkflow->record->where('group', 'price-lists')->count() == 0) {
        $idWorkflow = $mWorkflow->record->recordCreate(['name' => $this->translate('Price lists'), 'group' => 'price-lists'])['id'];
        /** @var WorkflowStep $mStep */
        $mStep = $this->getModel(WorkflowStep::class);
        $mStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Draft'), 'order' => 1, 'color' => '#9aa0a6', 'show_in_kanban' => true]);
        $mStep->record->recordCreate(['id_workflow' => $idWorkflow, 'name' => $this->translate('Approved'), 'order' => 2, 'color' => '#34a853', 'show_in_kanban' => true]);
      }

      // default sales price list based on product prices
      /** @var Models\PriceList $mPriceList */
      $mPriceList = $this->getModel(Models\PriceList::class);
      if ($mPriceList->record->count() == 0) {
        /** @var CurrencyService */
        $currencyService = $this->getService(CurrencyService::class);
        $idCurrency = $currencyService->getDefaultCurrencyId();
        if ($idCurrency > 0) {
          $mPriceList->record->recordCreate([
            'code' => 'RETAIL',
            'name' => $this->translate('Retail prices'),
            'type' => Models\PriceList::TYPE_SALES,
            'status' => Models\PriceList::STATUS_ACTIVE,
            'basis' => Models\PriceList::BASIS_PRODUCT_SALES_PRICE,
            'id_currency' => $idCurrency,
            'is_default' => true,
          ]);
        }
      }
    }
  }

  /**
   * Second-level sidebar of the app.
   *
   * @return string
   *
   */
  public function renderSecondSidebar(): string
  {
    /** @var Counter $counter */
    $counter = $this->getService(Counter::class);
    $draftCount = $counter->draftPriceLists();

    return '
      ' . $this->secondSidebarTitle() . '
      <div class="app-sidebar-buttons">
        ' . $this->secondSidebarButton('price-lists', 'fas fa-tags', 'Price lists', $draftCount) . '
        ' . $this->secondSidebarButton('price-lists/items', 'fas fa-list', 'Prices') . '
        ' . $this->secondSidebarButton('price-lists/assignments', 'fas fa-link', 'Assignments') . '
        ' . $this->secondSidebarButton('price-lists/rules', 'fas fa-percent', 'Rules') . '
        ' . $this->secondSidebarButton('price-lists/currency-rates', 'fas fa-coins', 'Currency rates') . '
        ' . $this->secondSidebarButton('price-lists/vat-rates', 'fas fa-receipt', 'VAT rates') . '
        ' . $this->secondSidebarButton('price-lists/simulator', 'fas fa-flask', 'Simulator') . '
        ' . $this->secondSidebarButton('price-lists/matrix', 'fas fa-table-cells', 'Matrix') . '
        ' . $this->secondSidebarButton('price-lists/histories', 'fas fa-clock-rotate-left', 'History') . '
        ' . $this->secondSidebarButton('price-lists/resolutions', 'fas fa-magnifying-glass-dollar', 'Resolutions') . '
        ' . $this->secondSidebarButton('calendar?show=price-lists', 'fas fa-calendar-days', 'Calendar') . '
        ' . $this->secondSidebarButton('settings/price-lists', 'fas fa-cog', 'Settings') . '
      </div>
    ';
  }

  /**
   * Alerts shown on the desktop.
   *
   * @return string
   *
   */
  public function renderAlerts(): string
  {
    /** @var Counter $counter */
    $counter = $this->getService(Counter::class);

    $expiring = count($counter->expiringPriceLists());
    $drafts = $counter->draftPriceLists();

    return
      ($expiring > 0 ? '
        <a href="' . $this->env()->projectUrl . '/price-lists?filters%5BfStatus%5D%5B0%5D=2&filters%5BfValidNow%5D%5B0%5D=1">'
          . $expiring . ' ' . $this->translate('price lists are about to expire')
        . '</a><br/>
      ' : '')
      . ($drafts > 0 ? '
        <a href="' . $this->env()->projectUrl . '/price-lists?filters%5BfStatus%5D%5B0%5D=1">'
          . $drafts . ' ' . $this->translate('price lists are waiting for activation')
        . '</a><br/>
      ' : '')
    ;
  }

  /**
   * Number shown in the badge of the app in the sidebar.
   *
   * @return int
   *
   */
  public function getSidebarBadgeNumber(): int
  {
    /** @var Counter $counter */
    $counter = $this->getService(Counter::class);
    return $counter->draftPriceLists() + count($counter->expiringPriceLists());
  }

  /**
   * Global search.
   *
   * @param array $expressions
   *
   * @return array
   *
   */
  public function search(array $expressions): array
  {
    /** @var Models\PriceList $mPriceList */
    $mPriceList = $this->getModel(Models\PriceList::class);
    $query = $mPriceList->record->prepareReadQuery();

    foreach ($expressions as $e) {
      $query = $query->orWhere($mPriceList->table . '.code', 'like', '%' . $e . '%');
      $query = $query->orWhere($mPriceList->table . '.name', 'like', '%' . $e . '%');
    }

    $results = [];
    foreach ($query->get()->toArray() as $priceList) {
      $results[] = [
        'id' => $priceList['id'],
        'label' => $priceList['code'] . ' ' . $priceList['name'],
        'url' => 'price-lists/' . $priceList['id'],
        'description' => $priceList['CURRENCY']['code'] ?? '',
      ];
    }

    return $results;
  }

  /**
   * Generates demo data: VAT rates, price lists, prices with quantity tiers, an assignment, a rule and a currency rate.
   *
   * @return void
   *
   */
  public function generateDemoData(): void
  {
    /** @var PriceCalculator $calc */
    $calc = $this->getService(PriceCalculator::class);
    /** @var CurrencyService */
    $currencyService = $this->getService(CurrencyService::class);

    /** @var Models\PriceList $mPriceList */
    $mPriceList = $this->getModel(Models\PriceList::class);
    /** @var Models\Item $mItem */
    $mItem = $this->getModel(Models\Item::class);
    /** @var Models\Assignment $mAssignment */
    $mAssignment = $this->getModel(Models\Assignment::class);
    /** @var Models\Rule $mRule */
    $mRule = $this->getModel(Models\Rule::class);

    $idCurrency = $currencyService->getDefaultCurrencyId();
    if ($idCurrency <= 0) {
      /** @var Currency $mCurrencyFirst */
      $mCurrencyFirst = $this->getModel(Currency::class);
      $idCurrency = (int) ($mCurrencyFirst->record->orderBy('id')->first()->id ?? 0);
    }
    if ($idCurrency <= 0) return;

    $retail = $mPriceList->record->where('code', 'RETAIL')->first();
    if (!$retail) {
      $retail = (object) $mPriceList->record->recordCreate([
        'code' => 'RETAIL', 'name' => $this->translate('Retail prices'), 'type' => Models\PriceList::TYPE_SALES,
        'status' => Models\PriceList::STATUS_ACTIVE, 'basis' => Models\PriceList::BASIS_PRODUCT_SALES_PRICE,
        'id_currency' => $idCurrency, 'is_default' => true,
      ]);
    }

    $wholesale = $mPriceList->record->recordCreate([
      'code' => 'WHOLESALE', 'name' => $this->translate('Wholesale prices'), 'type' => Models\PriceList::TYPE_SALES,
      'status' => Models\PriceList::STATUS_ACTIVE, 'basis' => Models\PriceList::BASIS_PARENT_LIST,
      'id_parent_price_list' => (int) $retail->id, 'derive_method' => Models\PriceList::DERIVE_PERCENT_OFF, 'derive_value' => 15,
      'rounding_policy' => Models\PriceList::ROUNDING_NEAREST, 'rounding_step' => 0.05, 'id_currency' => $idCurrency,
    ]);

    $vip = $mPriceList->record->recordCreate([
      'code' => 'VIP', 'name' => $this->translate('VIP customers'), 'type' => Models\PriceList::TYPE_SALES,
      'status' => Models\PriceList::STATUS_ACTIVE, 'basis' => Models\PriceList::BASIS_PARENT_LIST,
      'id_parent_price_list' => (int) $retail->id, 'derive_method' => Models\PriceList::DERIVE_PERCENT_OFF, 'derive_value' => 25,
      'id_currency' => $idCurrency,
    ]);

    $promo = $mPriceList->record->recordCreate([
      'code' => 'PROMO', 'name' => $this->translate('Autumn promotion'), 'type' => Models\PriceList::TYPE_SALES,
      'status' => Models\PriceList::STATUS_DRAFT, 'basis' => Models\PriceList::BASIS_PRODUCT_SALES_PRICE,
      'valid_from' => date('Y-m-d'), 'valid_to' => date('Y-m-d', strtotime('+30 days')), 'id_currency' => $idCurrency,
    ]);

    // quantity tiers on the first products
    /** @var Product $mProduct */
    $mProduct = $this->getModel(Product::class);
    $products = $mProduct->record->where('sales_price', '>', 0)->orderBy('id')->limit(5)->get();
    foreach ($products as $product) {
      $price = (float) $product->sales_price;
      foreach ([[10, 5], [50, 10], [100, 15]] as [$minQty, $discount]) {
        $mItem->record->recordCreate([
          'id_price_list' => (int) $wholesale['id'], 'id_product' => (int) $product->id, 'min_qty' => $minQty,
          'method' => Models\Item::METHOD_FIXED, 'value' => $calc->calculateDiscountedPrice($price, $discount),
        ]);
      }
    }

    // assignments: the VIP list for a customer tag (or the first customer), the wholesale list for another customer
    /** @var Tag $mTag */
    $mTag = $this->getModel(Tag::class);
    $tag = $mTag->record->orderBy('id')->first();
    if ($tag) {
      $mAssignment->record->recordCreate(['id_price_list' => (int) $vip['id'], 'id_customer_tag' => (int) $tag->id, 'priority' => 10]);
    }
    /** @var Customer $mCustomer */
    $mCustomer = $this->getModel(Customer::class);
    $customer = $mCustomer->record->orderBy('id')->first();
    if ($customer) {
      $mAssignment->record->recordCreate(['id_price_list' => (int) $wholesale['id'], 'id_customer' => (int) $customer->id, 'priority' => 5]);
    }

    // a rule: 10 % off for quantities of 20 and more
    $mRule->record->recordCreate([
      'name' => $this->translate('Volume discount 10 %'), 'id_price_list' => null, 'kind' => Models\Rule::KIND_PERCENT_OFF,
      'value' => 10, 'min_qty' => 20, 'is_stackable' => false, 'priority' => 5,
    ]);

    // a purchase list for the first supplier with purchase prices
    /** @var Supplier $mSupplier */
    $mSupplier = $this->getModel(Supplier::class);
    $supplier = $mSupplier->record->orderBy('id')->first();
    if ($supplier) {
      $purchase = $mPriceList->record->recordCreate([
        'code' => 'PURCHASE', 'name' => $this->translate('Purchase prices') . ' ' . $supplier->name, 'type' => Models\PriceList::TYPE_PURCHASE,
        'status' => Models\PriceList::STATUS_ACTIVE, 'basis' => Models\PriceList::BASIS_PRODUCT_PURCHASE_PRICE, 'id_currency' => $idCurrency,
      ]);
      $mAssignment->record->recordCreate(['id_price_list' => (int) $purchase['id'], 'id_supplier' => (int) $supplier->id]);

      /** @var ProductSupplier $mProductSupplier */
      $mProductSupplier = $this->getModel(ProductSupplier::class);
      foreach ($mProductSupplier->record->where('id_supplier', (int) $supplier->id)->limit(5)->get() as $ps) {
        $mItem->record->recordCreate([
          'id_price_list' => (int) $purchase['id'], 'id_product' => (int) $ps->id_product, 'id_supplier' => (int) $supplier->id,
          'method' => Models\Item::METHOD_FIXED, 'value' => (float) $ps->purchase_price,
        ]);
      }
    }
  }
}
