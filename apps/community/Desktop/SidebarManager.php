<?php

namespace HubletoApp\Community\Desktop;

class SidebarManager {
  const ITEM_LINK = 'link';
  const ITEM_DIVIDER = 'divider';
  const ITEM_HEADING_1 = 'heading_1';
  const ITEM_HEADING_2 = 'heading_2';

  public \HubletoMain $main;

  /** @var array<int, array<string, bool|string>> */
  public array $items = [];

  public function __construct(\HubletoMain $main) {
    $this->main = $main;
  }

  public function addItem(string $type, string $url, string $title, string $icon, bool $highlighted = false): void
  {
    $this->items[] = [
      'type' => $type,
      'url' => $url,
      'title' => $title,
      'icon' => $icon,
      'highlighted' => $highlighted,
    ];
  }

  public function addLink(string $url, string $title, string $icon, bool $highlighted = false): void
  {
    $this->addItem(self::ITEM_LINK, $url, $title, $icon, $highlighted);
  }

  public function addDivider(): void
  {
    $this->addItem(self::ITEM_DIVIDER, '', '', '');
  }

  public function addHeading1(string $title): void
  {
    $this->addItem(self::ITEM_HEADING_1, '', $title, '');
  }

  public function addHeading2(string $title): void
  {
    $this->addItem(self::ITEM_HEADING_2, '', $title, '');
  }

  public function getItems(): array
  {
    return $this->items;
  }

}