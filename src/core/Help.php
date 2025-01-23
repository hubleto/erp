<?php

namespace HubletoMain\Core;

class Help {
  public \HubletoMain $main;

  public array $hotTips = [];

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
  }

  public function addHotTip(string $slug, string $title): void
  {
    $this->hotTips[$slug] = $title;
  }
}
