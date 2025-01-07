<?php

namespace HubletoApp\Upgrade;

class Loader extends \HubletoCore\Core\Module
{


  // public static function canBeAdded(\HubletoCore $app): bool
  // {
  //   return !$app->isPro;
  // }

  public function __construct(\HubletoCore $app)
  {
    parent::__construct($app);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^upgrade\/?$/' => Controllers\Upgrade::class,
      '/^you-are-pro\/?$/' => Controllers\YouArePro::class,
    ]);

    // if (!$this->app->isPro) {
    //   $this->app->sidebar->addLink(1, 1000, 'upgrade', $this->translate('Upgrade'), 'fas fa-trophy', str_starts_with($this->app->requestedUri, 'upgrade'));
    // }
  }

}