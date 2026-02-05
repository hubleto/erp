<?php

namespace Hubleto\App\Community\Desktop;

class AppMenuManager extends \Hubleto\Erp\Core
{

  /** @var array<int, array<string, bool|string>> */
  public array $items = [];

  public function addItem(\Hubleto\Framework\Interfaces\AppInterface $app, string $url, string $title, string $icon): void
  {
    if ($app->isActivated) {
      $this->items[] = [
        'url' => $url,
        'title' => $title,
        'icon' => $icon,
      ];
    }
  }

  public function getItems(): array
  {
    return $this->items;
  }

}
