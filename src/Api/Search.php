<?php declare(strict_types=1);

namespace HubletoMain\Api;

use Exception;

class Search extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    return [
      ["id" => "1", "label" => "contacts", "url" => "contacts", ],
      ["id" => "2", "label" => "customers", "url" => "customers", ],
      ["id" => "3", "label" => "about", "url" => "about", ],
    ];
  }
}
