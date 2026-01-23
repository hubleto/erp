<?php

namespace Hubleto\App\Community\OAuth;

class Loader extends \Hubleto\Framework\App
{

  /**
   * Inits the app: adds routes, settings, calendars, event listeners, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^oauth\/authorize\/?$/' => Controllers\Authorize::class,
      '/^oauth\/token\/?$/' => Controllers\Token::class,
    ]);

    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => 'OAuth', // or $this->translate('OAuth')
      'icon' => 'fas fa-table',
      'url' => 'settings/oauth',
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\AccessToken::class)->dropTableIfExists()->install();
      $this->getModel(Models\AuthCode::class)->dropTableIfExists()->install();
      $this->getModel(Models\Client::class)->dropTableIfExists()->install();
      $this->getModel(Models\RefreshToken::class)->dropTableIfExists()->install();
      $this->getModel(Models\Scope::class)->dropTableIfExists()->install();
    }
    if ($round == 2) {
      $mClient = $this->getModel(Models\Client::class);
      $mClient->record->recordCreate([
        'client_id' => 'test_client_id',
        'client_secret' => 'test_client_secret',
        'name' => 'test client',
        'redirect_uri' => 'test_redirect_uri',
      ]);
    }
  }

}
