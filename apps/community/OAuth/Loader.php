<?php

namespace HubletoApp\Community\OAuth;

class Loader extends \HubletoMain\Core\App
{

  // Uncomment following if you want a button for app's settings
  // to be rendered next in sidebar, right next to your app's button.
  // public bool $hasCustomSettings = true;

  // init
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^oauth\/authorize\/?$/' => Controllers\Authorize::class,
      '/^oauth\/token\/?$/' => Controllers\Token::class,
    ]);

    // Add placeholder for custom settings.
    // This will be displayed in the Settings app, under the "All settings" card.
    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => 'OAuth', // or $this->translate('OAuth')
      'icon' => 'fas fa-table',
      'url' => 'settings/oauth',
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\AuthCode($this->main))->dropTableIfExists()->install();
    }
  }

}