<?php

namespace HubletoMain\Core;

class Help {
  public \HubletoMain $main;

  public array $hotTips = [];

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
  }

  public function addHotTip($slug, $title) {
    $this->hotTips[$slug] = $title;
  }
}
