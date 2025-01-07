<?php

namespace HubletoApp\Upgrade;

class Loader extends \HubletoMain\Core\Module
{


  // public static function canBeAdded(\HubletoMain $app): bool
  // {
  //   return !$app->isPro;
  // }

  public function __construct(\HubletoMain $app)
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