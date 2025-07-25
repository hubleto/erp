<?php

namespace HubletoApp\Community\OAuth\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Boolean;

class AuthCode extends \Hubleto\Framework\Models\Model
{
  public string $table = 'oauth_auth_codes';
  public string $recordManagerClass = RecordManagers\AuthCode::class;
  public ?string $lookupSqlValue = 'concat("AuthCode #", {%TABLE%}.id)';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'code' => (new Varchar($this, $this->translate('Code')))->setProperty('defaultVisibility', true),
      'expires_at' => (new Varchar($this, $this->translate('Expires At')))->setProperty('defaultVisibility', true),
      'user_id' => (new Varchar($this, $this->translate('User Id')))->setProperty('defaultVisibility', true),
      'client_id' => (new Varchar($this, $this->translate('Client Id')))->setProperty('defaultVisibility', true),
      'scopes' => (new Varchar($this, $this->translate('Scopes')))->setProperty('defaultVisibility', true),
      'code_challenge' => (new Varchar($this, $this->translate('Code Challenge')))->setProperty('defaultVisibility', true),
      'code_challenge_method' => (new Varchar($this, $this->translate('Code Challenge Method')))->setProperty('defaultVisibility', true),
      'redirect_uri' => (new Varchar($this, $this->translate('Redirect Uri')))->setProperty('defaultVisibility', true),
      'revoked' => (new Boolean($this, $this->translate('Revoked')))->setProperty('defaultVisibility', true),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add AuthCode';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }

}
