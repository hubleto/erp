<?php declare(strict_types=1);

namespace Hubleto\Erp\Api;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Helper;

class GetLocale extends \Hubleto\Erp\Controllers\ApiController
{
  public bool $permittedForAllUsers = true;

  public function renderJson(): array
  {
    return $this->locale()->getAll();
  }

}
