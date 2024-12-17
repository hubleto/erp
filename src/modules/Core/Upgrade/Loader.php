<?php

namespace CeremonyCrmMod\Core\Upgrade;

class Loader extends \CeremonyCrmApp\Core\Module
{


  // public static function canBeAdded(\CeremonyCrmApp $app): bool
  // {
  //   return !$app->isPro;
  // }

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^upgrade\/?$/' => Controllers\Upgrade::class,
      '/^you-are-pro\/?$/' => Controllers\YouArePro::class,
    ]);

    if (!$this->app->isPro) {
      $this->app->sidebar->addLink(1, 1000, 'upgrade', $this->translate('Upgrade'), 'fas fa-trophy', str_starts_with($this->app->requestedUri, 'upgrade'));
    }
  }

}