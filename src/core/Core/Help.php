<?php

namespace CeremonyCrmApp\Core;

class Help {
  public \CeremonyCrmApp $app;

  public array $hotTips = [];

  public function __construct(\CeremonyCrmApp $app)
  {
    $this->app = $app;
  }

  public function addHotTip($slug, $title) {
    $this->hotTips[$slug] = $title;
  }
}
