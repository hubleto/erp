<?php declare(strict_types=1);

namespace HubletoMain;

class Extendible extends CoreClass
{
  public App $app;
  public array $items = [];

  public function getItems(): array
  {
    return [];
  }

}
