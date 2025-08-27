<?php

namespace Hubleto\App\Community\Invoices\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\HasOne;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

use \Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use \Hubleto\App\Community\Settings\Models\RecordManagers\User;
use \Hubleto\App\Community\Settings\Models\RecordManagers\InvoiceProfile;
use Hubleto\App\Community\Pipeline\Models\RecordManagers\Pipeline;
use Hubleto\App\Community\Pipeline\Models\RecordManagers\PipelineStep;
use Hubleto\App\Community\Documents\Models\RecordManagers\Template;

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

  /** @return HasOne<Pipeline, covariant Deal> */
  public function PIPELINE(): HasOne
  {
    return $this->hasOne(Pipeline::class, 'id', 'id_pipeline');
  }

  /** @return HasOne<PipelineStep, covariant Deal> */
  public function PIPELINE_STEP(): HasOne
  {
    return $this->hasOne(PipelineStep::class, 'id', 'id_pipeline_step');
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

    $idCustomer = $main->getRouter()->urlParamAsInteger('idCustomer');
    if ($idCustomer > 0) $query->where('id_customer', $idCustomer);

    $idProfile = $main->getRouter()->urlParamAsInteger('idProfile');
    if ($idProfile > 0) $query->where('id_profil', $idProfile);

    $query = Pipeline::applyPipelineStepDefaultFilter(
      $this->model,
      $query,
      $defaultFilters['fInvoicePipelineStep'] ?? []
    );

    if ($main->getRouter()->isUrlParam('number')) $query->where('number', 'like', '%' . $main->getRouter()->urlParamAsString('number') . '%');
    if ($main->getRouter()->isUrlParam('vs')) $query->where('vs', 'like', '%' . $main->getRouter()->urlParamAsString('vs') . '%');

    if ($main->getRouter()->isUrlParam('dateIssueFrom')) $query->whereDate('date_issue', '>=', $main->getRouter()->urlParamAsString('dateIssueFrom'));
    if ($main->getRouter()->isUrlParam('dateIssueTo')) $query->whereDate('date_issue', '<=', $main->getRouter()->urlParamAsString('dateIssueTo'));
    if ($main->getRouter()->isUrlParam('dateDeliveryFrom')) $query->whereDate('date_delivery', '>=', $main->getRouter()->urlParamAsString('dateDeliveryFrom'));
    if ($main->getRouter()->isUrlParam('dateDeliveryTo')) $query->whereDate('date_delivery', '<=', $main->getRouter()->urlParamAsString('dateDeliveryTo'));
    if ($main->getRouter()->isUrlParam('dateTueFrom')) $query->whereDate('date_due', '>=', $main->getRouter()->urlParamAsString('dateTueFrom'));
    if ($main->getRouter()->isUrlParam('dateTueTo')) $query->whereDate('date_due', '<=', $main->getRouter()->urlParamAsString('dateTueTo'));
    if ($main->getRouter()->isUrlParam('datePaymentFrom')) $query->whereDate('date_payment', '>=', $main->getRouter()->urlParamAsString('datePaymentFrom'));
    if ($main->getRouter()->isUrlParam('datePaymentTo')) $query->whereDate('date_payment', '<=', $main->getRouter()->urlParamAsString('datePaymentTo'));

    $query
      ->first()
      ?->toArray()
    ;

    return $query;
  }

}
