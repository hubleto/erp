<?php

namespace HubletoApp\Community\Desktop;

class AppMenuManager {

  public \HubletoMain $main;

  /** @var array<int, array<string, bool|string>> */
  public array $items = [];

  public function __construct(\HubletoMain $main) {
    $this->main = $main;
  }

  public function addItem(string $url, string $title, string $icon): void
  {
    $this->items[] = [
      'url' => $url,
      'title' => $title,
      'icon' => $icon,
    ];
  }

  public function getItems(): array
  {
    return $this->items;
  }

}