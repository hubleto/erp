<?php

namespace Hubleto\App\Community\OAuth\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Boolean;

class AuthCode extends \Hubleto\Erp\Model
{
  public string $table = 'oauth_auth_codes';
  public string $recordManagerClass = RecordManagers\AuthCode::class;
  public ?string $lookupSqlValue = 'concat("AuthCode #", {%TABLE%}.id)';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'code' => (new Varchar($this, $this->translate('Code')))->setDefaultVisible(),
      'expires_at' => (new Varchar($this, $this->translate('Expires At')))->setDefaultVisible(),
      'user_id' => (new Varchar($this, $this->translate('User Id')))->setDefaultVisible(),
      'client_id' => (new Varchar($this, $this->translate('Client Id')))->setDefaultVisible(),
      'scopes' => (new Varchar($this, $this->translate('Scopes')))->setDefaultVisible(),
      'code_challenge' => (new Varchar($this, $this->translate('Code Challenge')))->setDefaultVisible(),
      'code_challenge_method' => (new Varchar($this, $this->translate('Code Challenge Method')))->setDefaultVisible(),
      'redirect_uri' => (new Varchar($this, $this->translate('Redirect Uri')))->setDefaultVisible(),
      'revoked' => (new Boolean($this, $this->translate('Revoked')))->setDefaultVisible(),
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
