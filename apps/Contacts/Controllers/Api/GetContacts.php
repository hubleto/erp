<?php

namespace Hubleto\App\Community\Contacts\Controllers\Api;

use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Contacts\Models\ContactTag;
use Hubleto\App\Community\Contacts\Models\Tag;

class GetContacts extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    return [
      'status' => 'success',
      'contacts' => [],
    ];
  }
}
