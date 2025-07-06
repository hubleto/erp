<?php

namespace HubletoApp\Community\Reports;

class Loader extends \HubletoMain\Core\App
{

  public ReportManager $reportManager;

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
    $this->reportManager = new ReportManager($main);
  }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^reports\/?$/' => Controllers\Home::class,
      '/^reports\/(?<reportUrlSlug>.*?)\/?$/' => Controllers\Report::class,
      // '/^reports\/(?<reportUrlSlug>.*?)\/load-data\/?$/' => Controllers\ReportLoadData::class,
      // '/^reports\/(?<reportUrlSlug>.*?)\/load-data\/?$/' => Controllers\ReportLoadData::class,
    ]);

  }

}