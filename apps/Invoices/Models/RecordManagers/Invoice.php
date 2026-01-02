<?php

namespace Hubleto\App\Community\Invoices\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;

use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Hubleto\App\Community\Settings\Models\RecordManagers\Currency;
use Hubleto\App\Community\Invoices\Models\RecordManagers\Profile;
use Hubleto\App\Community\Workflow\Models\RecordManagers\Workflow;
use Hubleto\App\Community\Workflow\Models\RecordManagers\WorkflowStep;
use Hubleto\App\Community\Documents\Models\RecordManagers\Template;
use Hubleto\App\Community\Invoices\Models\RecordManagers\Payment;
use Hubleto\App\Community\Suppliers\Models\RecordManagers\Supplier;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends \Hubleto\Erp\RecordManager {
  public $table = 'invoices';

  /** @return BelongsTo<Customer, covariant Invoice> */
  public function CUSTOMER(): BelongsTo {
    return $this->BelongsTo(Customer::class, 'id_customer');
  }

  /** @return BelongsTo<Customer, covariant Invoice> */
  public function SUPPLIER(): BelongsTo {
    return $this->BelongsTo(Supplier::class, 'id_supplier');
  }

  /** @return BelongsTo<Customer, covariant Invoice> */
  public function PAYMENT_METHOD(): BelongsTo {
    return $this->BelongsTo(PaymentMethod::class, 'id_payment_method');
  }

  /** @return BelongsTo<Profile, covariant Invoice> */
  public function PROFILE(): BelongsTo {
    return $this->BelongsTo(Profile::class, 'id_profile');
  }

  /** @return BelongsTo<User, covariant Invoice> */
  public function ISSUED_BY(): BelongsTo {
    return $this->BelongsTo(User::class, 'id_issued_by');
  }

  /** @return HasOne<Currency, covariant Deal> */
  public function CURRENCY(): HasOne
  {
    return $this->hasOne(Currency::class, 'id', 'id_currency');
  }

  /** @return HasOne<Workflow, covariant Deal> */
  public function WORKFLOW(): HasOne
  {
    return $this->hasOne(Workflow::class, 'id', 'id_workflow');
  }

  /** @return HasOne<WorkflowStep, covariant Deal> */
  public function WORKFLOW_STEP(): HasOne
  {
    return $this->hasOne(WorkflowStep::class, 'id', 'id_workflow_step');
  }

  /** @return hasOne<Currency, covariant Lead> */
  public function TEMPLATE(): HasOne
  {
    return $this->hasOne(Template::class, 'id', 'id_template');
  }

  /** @return HasMany<Item, covariant Invoice> */
  public function ITEMS(): HasMany {
    return $this->HasMany(Item::class, 'id_invoice');
  }

  /** @return HasMany<Item, covariant Invoice> */
  public function PAYMENTS(): HasMany {
    return $this->HasMany(Payment::class, 'id_invoice');
  }

  /**
   * [Description for prepareReadQuery]
   *
   * @param mixed|null $query
   * @param int $level
   * 
   * @return mixed
   * 
   */
  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $filters = $hubleto->router()->urlParamAsArray("filters");

    $idCustomer = $hubleto->router()->urlParamAsInteger('idCustomer');
    if ($idCustomer > 0) $query->where('id_customer', $idCustomer);

    $idProfile = $hubleto->router()->urlParamAsInteger('idProfile');
    if ($idProfile > 0) $query->where('id_profile', $idProfile);

    if (isset($filters["fInboundOutbound"]) && $filters["fInboundOutbound"] > 0) {
      $query = $query->where("invoices.inbound_outbound", $filters["fInboundOutbound"]);
    }

    if (!empty($filters['fInboundOutbound'])) {
      $query = $query->where("invoices.inbound_outbound", $filters['fInboundOutbound']);
    }

    if (!empty($filters['fType'])) {
      $query = $query->where("invoices.type", $filters['fType']);
    }

    switch ($filters["fDue"] ?? 0) {
      case 1: $query = $query->whereDate($this->table . ".date_due", "<=", date("Y-m-d")); break;
      case 2: $query = $query->whereDate($this->table . ".date_due", ">", date("Y-m-d")); break;
    }

    switch ($filters["fPaid"] ?? 0) {
      case 1: $query = $query->whereNotNull($this->table . ".date_payment"); break;
      case 2: $query = $query->whereNull($this->table . ".date_payment"); break;
    }

    if (isset($filters['fIssued'])) {
      switch ($filters['fIssued']) {
        case 'today': $query = $query->whereDate('date_due', date('Y-m-d')); break;
        case 'yesterday': $query = $query->whereDate('date_due', date('Y-m-d', strtotime('-1 day'))); break;
        case 'last7Days': $query = $query->whereDate('date_due', '>=', date('Y-m-d', strtotime('-7 days'))); break;
        case 'last14Days': $query = $query->whereDate('date_due', '>=', date('Y-m-d', strtotime('-14 days'))); break;
        case 'thisMonth': $query = $query->whereMonth('date_due', date('m')); break;
        case 'lastMonth': $query = $query->whereMonth('date_due', date('m', strtotime('-1 month'))); break;
        case 'beforeLastMonth': $query = $query->whereMonth('date_due', date('m', strtotime('-2 month'))); break;
        case 'thisYear': $query = $query->whereYear('date_due', date('Y')); break;
        case 'lastYear': $query = $query->whereYear('date_due', date('Y') - 1); break;
      }
    }

    $query = Workflow::applyWorkflowStepFilter(
      $this->model,
      $query,
      (array) ($filters['fInvoiceWorkflowStep'] ?? [])
    );

    if ($hubleto->router()->urlParamNotEmpty('number')) $query->where('number', 'like', '%' . $hubleto->router()->urlParamAsString('number') . '%');
    if ($hubleto->router()->urlParamNotEmpty('vs')) $query->where('vs', 'like', '%' . $hubleto->router()->urlParamAsString('vs') . '%');

    if ($hubleto->router()->urlParamNotEmpty('dateIssueFrom')) $query->whereDate('date_issue', '>=', $hubleto->router()->urlParamAsString('dateIssueFrom'));
    if ($hubleto->router()->urlParamNotEmpty('dateIssueTo')) $query->whereDate('date_issue', '<=', $hubleto->router()->urlParamAsString('dateIssueTo'));
    if ($hubleto->router()->urlParamNotEmpty('dateDeliveryFrom')) $query->whereDate('date_delivery', '>=', $hubleto->router()->urlParamAsString('dateDeliveryFrom'));
    if ($hubleto->router()->urlParamNotEmpty('dateDeliveryTo')) $query->whereDate('date_delivery', '<=', $hubleto->router()->urlParamAsString('dateDeliveryTo'));
    if ($hubleto->router()->urlParamNotEmpty('dateTueFrom')) $query->whereDate('date_due', '>=', $hubleto->router()->urlParamAsString('dateTueFrom'));
    if ($hubleto->router()->urlParamNotEmpty('dateTueTo')) $query->whereDate('date_due', '<=', $hubleto->router()->urlParamAsString('dateTueTo'));
    if ($hubleto->router()->urlParamNotEmpty('datePaymentFrom')) $query->whereDate('date_payment', '>=', $hubleto->router()->urlParamAsString('datePaymentFrom'));
    if ($hubleto->router()->urlParamNotEmpty('datePaymentTo')) $query->whereDate('date_payment', '<=', $hubleto->router()->urlParamAsString('datePaymentTo'));

    // $query
    //   ->first()
    //   ?->toArray()
    // ;

    return $query;
  }

}
