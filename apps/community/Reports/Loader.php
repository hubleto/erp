<?php

namespace HubletoApp\Community\Reports;

class Loader extends \HubletoMain\Core\App
{

  public function init(): void
  {
    $this->main->router->httpGet([
      '/^reports\/?$/' => Controllers\Home::class,
      '/^reports\/(?<reportUrlSlug>.*?)\/?$/' => Controllers\Report::class,
    ]);

    $this->main->sidebar->addLink(1, 2200, 'reports', $this->translate('Reports'), 'fas fa-square-poll-vertical', str_starts_with($this->main->requestedUri, 'reports'));
  }

}