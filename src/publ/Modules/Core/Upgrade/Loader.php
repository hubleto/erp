<?php

namespace CeremonyCrmApp\Modules\Core\Upgrade;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public string $translationContext = 'mod.core.upgrade.loader';

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
      $this->app->sidebar->addLink(1, 100100, 'upgrade', $this->translate('Upgrade'), 'fas fa-trophy');
    }
  }

}