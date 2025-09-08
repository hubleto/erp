<?php

namespace Hubleto\App\Community\Pipeline;

class Pipeline extends \Hubleto\Framework\Core
{
  public \Hubleto\Framework\Interfaces\AppInterface $app;

  public function loadItems(int $idPipeline, array $filters): array
  {
    return [];
  }
}
