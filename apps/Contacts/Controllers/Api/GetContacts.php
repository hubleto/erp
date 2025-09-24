<?php

namespace Hubleto\App\Community\Contacts\Controllers\Api;

use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Contacts\Models\ContactTag;
use Hubleto\App\Community\Contacts\Models\Tag;

class GetContacts extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idCustomer = $this->router()->urlParamAsInteger('idCustomer');

    /** @var Contact */
    $mContact = $this->getModel(Contact::class);

    $query = $mContact->record;
    if ($idCustomer > 0) $query = $query->where('id_customer', $idCustomer);

    $contacts = $query->get();

    return [
      'status' => 'success',
      'contacts' => $contacts,
    ];
  }
}
