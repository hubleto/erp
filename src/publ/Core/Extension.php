<?php

namespace CeremonyCrmApp\Core;

class Extension
{
  public \CeremonyCrmApp $app;
  public string $rootFolder;

  public function __construct(\CeremonyCrmApp $app)
  {
    $this->app = $app;

    $this->rootFolder = realpath(pathinfo((new \ReflectionClass(get_class($this)))->getFileName(), PATHINFO_DIRNAME) . '/..');
  }

  public function getUrlBase(): string
  {
    return 'set-your-url-base-' . rand(1000, 9000);
  }

  public function getRoutes(): array
  {
    return [
      '' => 'Home',
    ];
  }

}