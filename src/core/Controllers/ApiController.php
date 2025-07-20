<?php

namespace HubletoMain\Core\Controllers;

class ApiController extends Controller
{
  public int $returnType = self::RETURN_TYPE_JSON;
  public bool $permittedForAllUsers = true;
  public bool $disableLogUsage = true;

  public function response(): array
  {
    return [];
  }

  public function renderJson(): ?array
  {
    try {
      return $this->response();
    } catch (\Throwable $e) {
      http_response_code(400);

      return [
        'status' => 'error',
        'code' => $e->getCode(),
        'message' => $e->getMessage(),
      ];
    }
  }
}
