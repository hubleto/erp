<?php

namespace Hubleto\App\Community\Invoices\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Hubleto\App\Community\Documents\Models\RecordManagers\Template;
use Hubleto\App\Community\Settings\Models\RecordManagers\Currency;
use Hubleto\App\Community\Settings\Models\RecordManagers\InvoiceProfile;
use Hubleto\App\Community\Workflow\Models\RecordManagers\Workflow;
use Hubleto\App\Community\Workflow\Models\RecordManagers\WorkflowStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends \Hubleto\Erp\RecordManager {
  public $table = 'invoices';

  /** @return BelongsTo<Customer, covariant Invoice> */
  public function CUSTOMER(): BelongsTo {
    return $this->BelongsTo(Customer::class, 'id_customer');
  }

  /** @return BelongsTo<InvoiceProfile, covariant Invoice> */
  public function PROFILE(): BelongsTo {
    return $this->BelongsTo(InvoiceProfile::class, 'id_profile');
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
  /** @return HasMany<InvoiceItem, covariant Invoice> */
  public function ITEMS(): HasMany {
    return $this->HasMany(InvoiceItem::class, 'id_invoice');
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
  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    $idCustomer = $main->router()->urlParamAsInteger('idCustomer');
    if ($idCustomer > 0) $query->where('id_customer', $idCustomer);

    $idProfile = $main->router()->urlParamAsInteger('idProfile');
    if ($idProfile > 0) $query->where('id_profil', $idProfile);

    $query = Workflow::applyWorkflowStepFilter(
      $this->model,
      $query,
      $filters['fInvoiceWorkflowStep'] ?? []
    );

    if ($main->router()->isUrlParam('number')) $query->where('number', 'like', '%' . $main->router()->urlParamAsString('number') . '%');
    if ($main->router()->isUrlParam('vs')) $query->where('vs', 'like', '%' . $main->router()->urlParamAsString('vs') . '%');

    if ($main->router()->isUrlParam('dateIssueFrom')) $query->whereDate('date_issue', '>=', $main->router()->urlParamAsString('dateIssueFrom'));
    if ($main->router()->isUrlParam('dateIssueTo')) $query->whereDate('date_issue', '<=', $main->router()->urlParamAsString('dateIssueTo'));
    if ($main->router()->isUrlParam('dateDeliveryFrom')) $query->whereDate('date_delivery', '>=', $main->router()->urlParamAsString('dateDeliveryFrom'));
    if ($main->router()->isUrlParam('dateDeliveryTo')) $query->whereDate('date_delivery', '<=', $main->router()->urlParamAsString('dateDeliveryTo'));
    if ($main->router()->isUrlParam('dateTueFrom')) $query->whereDate('date_due', '>=', $main->router()->urlParamAsString('dateTueFrom'));
    if ($main->router()->isUrlParam('dateTueTo')) $query->whereDate('date_due', '<=', $main->router()->urlParamAsString('dateTueTo'));
    if ($main->router()->isUrlParam('datePaymentFrom')) $query->whereDate('date_payment', '>=', $main->router()->urlParamAsString('datePaymentFrom'));
    if ($main->router()->isUrlParam('datePaymentTo')) $query->whereDate('date_payment', '<=', $main->router()->urlParamAsString('datePaymentTo'));

    $query
      ->first()
      ?->toArray()
    ;

    return $query;
  }

}
