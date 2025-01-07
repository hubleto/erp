<?php

namespace HubletoMain\Core;

class Help {
  public \HubletoMain $app;

  public array $hotTips = [];

  public function __construct(\HubletoMain $app)
  {
    $this->app = $app;
  }

  public function addHotTip($slug, $title) {
    $this->hotTips[$slug] = $title;
  }
}
