<?php

namespace HubletoApp\Community\Desktop;

class AppMenuManager {

  public \HubletoMain $main;

  /** @var array<int, array<string, bool|string>> */
  public array $items = [];

  public function __construct(\HubletoMain $main) {
    $this->main = $main;
  }

  public function addItem(\HubletoMain\Core\App $app, string $url, string $title, string $icon): void
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