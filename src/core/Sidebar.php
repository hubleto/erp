<?php

namespace CeremonyCrmApp\Core;

class Sidebar {
  const ITEM_LINK = 'link';
  const ITEM_DIVIDER = 'divider';
  const ITEM_HEADING_1 = 'heading_1';
  const ITEM_HEADING_2 = 'heading_2';

  public \CeremonyCrmApp $app;
  public array $items = [];

  public function __construct(\CeremonyCrmApp $app) {
    $this->app = $app;
    $this->items[1] = [];
    $this->items[2] = [];
  }

  public function addItem(int $level, int $key, string $type, string $url, string $title, string $icon, $highlighted = false) {
    $this->items[$level][$key] = [
      'type' => $type,
      'url' => $url,
      'title' => $title,
      'icon' => $icon,
      'highlighted' => $highlighted,
    ];

    ksort($this->items[$level]);
  }

  public function addLink(int $level, int $key, string $url, string $title, string $icon, $highlighted = false) {
    $this->addItem($level, $key, self::ITEM_LINK, $url, $title, $icon, $highlighted);
  }

  public function addDivider(int $level, int $key) {
    $this->addItem($level, $key, self::ITEM_DIVIDER);
  }

  public function addHeading1(int $level, int $key, string $title) {
    $this->addItem($level, $key, self::ITEM_HEADING_1, '', $title, '');
  }

  public function addHeading2(int $level, int $key, string $title) {
    $this->addItem($level, $key, self::ITEM_HEADING_2, '', $title, '');
  }

  public function getItems(int $level): array {
    return $this->items[$level] ?? [];
  }

}