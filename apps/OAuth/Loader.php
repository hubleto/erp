<?php

namespace HubletoApp\Community\OAuth;

class Loader extends \Hubleto\Framework\App
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
      (new Models\AccessToken($this->main))->dropTableIfExists()->install();
      (new Models\AuthCode($this->main))->dropTableIfExists()->install();
      (new Models\Client($this->main))->dropTableIfExists()->install();
      (new Models\RefreshToken($this->main))->dropTableIfExists()->install();
      (new Models\Scope($this->main))->dropTableIfExists()->install();
    }
    if ($round == 1) {
      $mClient = $this->main->di->create(Models\Client::class);
      $mClient->record->recordCreate([
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'name' => 'test client',
        'redirect_uri' => 'test_redirect_uri',
      ]);
    }
  }

}
