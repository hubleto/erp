<?php

namespace CeremonyCrmApp\Core;

class Sidebar {
  const ITEM_LINK = 'link';
  const ITEM_DIVIDER = 'divider';
  const ITEM_HEADING_1 = 'heading_1';
  const ITEM_HEADING_2 = 'heading_2';

  public \CeremonyCrmApp $app;
  protected array $items = [];

  public function __construct(\CeremonyCrmApp $app) {
    $this->app = $app;
    $this->items[1] = [];
    $this->items[2] = [];

    array_walk($app->getRegisteredModules(), function($moduleClass) {
      $module = new $moduleClass($this->app);
      $module->modifySidebar($this);
    });
  }

  public function addItem( int $level, string $type, string $url, string $title, string $icon) {
    $this->items[$level][] = [
      'type' => $type,
      'url' => $url,
      'title' => $title,
      'icon' => $icon
    ];
  }

  public function addLink(int $level, string $url, string $title, string $icon) {
    $this->addItem($level, self::ITEM_LINK, $url, $title, $icon);
  }

  public function addDivider(int $level) {
    $this->addItem($level, self::ITEM_DIVIDER);
  }

  public function addHeading1(int $level, string $title) {
    $this->addItem($level, self::ITEM_HEADING_1, '', $title, '');
  }

  public function addHeading2(int $level, string $title) {
    $this->addItem($level, self::ITEM_HEADING_2, '', $title, '');
  }

  public function getItems(int $level): array {
    return $this->items[$level] ?? [];
  }

}