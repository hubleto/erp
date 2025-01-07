<?php

namespace HubletoCore\Core;

class Help {
  public \HubletoCore $app;

  public array $hotTips = [];

  public function __construct(\HubletoCore $app)
  {
    $this->app = $app;
  }

  public function addHotTip($slug, $title) {
    $this->hotTips[$slug] = $title;
  }
}
