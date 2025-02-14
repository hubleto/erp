<?php

namespace HubletoApp\Community\Reports;

class Loader extends \HubletoMain\Core\App
{

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^reports\/?$/' => Controllers\Home::class,
      '/^reports\/(?<reportUrlSlug>.*?)\/?$/' => Controllers\Report::class,
    ]);

  }

}